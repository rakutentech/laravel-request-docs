// noinspection t

import React, {useEffect, useState} from 'react';

import useLocalStorage from 'react-use-localstorage';
import {makeCurlCommand} from '../libs/strings'
import type {IAPIInfo, IConfig, LRDResponse} from '../libs/types'
import ApiActionResponse from './elements/ApiActionResponse'
import ApiActionRequest from './elements/ApiActionRequest'
import ApiActionTabs from './elements/ApiActionTabs'
import ApiActionInfo from './elements/ApiActionInfo'
import ApiActionSQL from './elements/ApiActionSQL'
import ApiActionLog from './elements/ApiActionLog'
import ApiActionEvents from './elements/ApiActionEvents'
import {objectToFormData} from '../libs/object';

interface Props {
    lrdDocsItem: IAPIInfo,
    method: string,
    host: string,
    config: IConfig,
}
export default function ApiAction(props: Props) {
    const { lrdDocsItem, method, host, config } = props
    const [error, setError] = useState<string | null>(null);

    const [allParamsRegistry, setAllParamsRegistery] = useLocalStorage('allParamsRegistry', "{}");

    const [requestHeaders, setRequestHeaders] = useLocalStorage('requestHeaders', JSON.stringify(config.default_headers, null, 2));
    const [curlCommand, setCurlCommand] = useState("");
    const [requestUri, setRequestUri] = useState(lrdDocsItem.uri);
    const [timeTaken, setTimeTaken] = useState(0);
    const [sendingRequest, setSendingRequest] = useState(false);
    const [queryParams, setQueryParams] = useState('');
    const [bodyParams, setBodyParams] = useState('');
    const [fileParams, setFileParams] = useState<FormData>();
    const [responseData, setResponseData] = useState("");
    const [sqlQueriesCount, setSqlQueriesCount] = useState(0);
    const [sqlData, setSqlData] = useState("");
    const [modelsData, setModelsData] = useState({
        modelsSummary: [],
        modelsTimeline: []
    });
    const [logsData, setLogsData] = useState("");
    const [serverMemory, setServerMemory] = useState("");
    const [responseStatus, setResponseStatus] = useState(0);
    const [responseHeaders, setResponseHeaders] = useState("");
    const [activeTab, setActiveTab] = useState('info');

    const handleFileChange = (files: any, key: any) => {
        const formData = fileParams || new FormData();
        const parts = key.split('.');
        const fileKey = key.split(".").reduce((current: string, part: string, index: number) => {
            if (index === parts.length - 1 && (part === '*' || !isNaN(Number(part)))) {
                return current
            }
            return !current ? part : `${current}[${part}]`
        }, '')
        if (key.includes('.*')) {
            for (let i = 0; i < files.length; i++) {
                formData.append(`${fileKey}[${i}]`, files[i]);
            }
        } else {
            formData.append(fileKey, files[0])
        }
        setFileParams(formData)
    }

    // // update localstorage
    const updateLocalStorage = () => {
        const jsonAllParamsRegistry = JSON.parse(allParamsRegistry)
        if (method == 'GET' || method == 'HEAD' || method == 'DELETE') {
            jsonAllParamsRegistry[method + "-" + lrdDocsItem.uri] = queryParams
        }
        if (method == 'POST' || method == 'PUT' || method == 'PATCH') {
            jsonAllParamsRegistry[method + "-" + lrdDocsItem.uri] = bodyParams
        }

        setAllParamsRegistery(JSON.stringify(jsonAllParamsRegistry))
    }

    const handleSendRequest = () => {
        updateLocalStorage()
        try {
            JSON.parse(requestHeaders)
        } catch (error: any) {
            setError("Global Request Headers are incorrect: " + error.message)
            return
        }
        const headers = JSON.parse(requestHeaders)
        headers['X-Request-LRD'] = true
        if (fileParams) {
            delete headers['Content-Type']
            // headers['Accept'] = 'multipart/form-data'
        }

        const options: any = {
            credentials: "include",
            method: method,
            headers: headers,
        }


        if (method == 'POST' || method == 'PUT' || method == 'PATCH') {
            try {
                if (fileParams != null) {
                    objectToFormData(JSON.parse(bodyParams), fileParams as FormData)
                }

            } catch (error: any) {
                setError("Request body incorrect: " + error.message)
                return
            }

            if (fileParams != null) {
                options['body'] = fileParams // includes body as well
            } else {
                options['body'] = bodyParams // just the body
            }
        }

        const startTime = performance.now();

        setSendingRequest(true)
        setSqlData("")
        setLogsData("")
        setServerMemory("")
        setResponseData("")
        setError(null)

        fetch(`${host}/${requestUri}${queryParams}`, options)
            .then((response) => {
                let timeTaken = performance.now() - startTime
                // round to 3 decimals
                timeTaken = Math.round((timeTaken + Number.EPSILON) * 1000) / 1000
                setTimeTaken(timeTaken)
                setResponseStatus(response.status)
                setResponseHeaders(JSON.stringify(Object.fromEntries(response.headers), null, 2))
                setSendingRequest(false)
                return response.text()
            }).then((dataString) => {
                let isJson = true
                let data
                try {
                    data = JSON.parse(dataString) as LRDResponse
                } catch (error: any) {
                    isJson = false
                    // do nothing
                }

                const wasDataWrapped = (data && data._lrd)

                if (data && data._lrd && data._lrd.queries) {
                    const sqlQueries = data._lrd.queries.map((query: any) => {
                        return "Connection: "
                            + query.connection_name
                            + " Time taken: "
                            + query.time
                            + "ms: \n"
                            + query.sql + "\n"
                    }).join("\n")
                    setSqlData(sqlQueries)
                    setSqlQueriesCount(data._lrd.queries.length)
                }
                if (data && data._lrd && data._lrd.logs) {
                    let logs = ""
                    for (const value of data._lrd.logs) {
                        logs += value.level + ": " + value.message + "\n"
                    }
                    setLogsData(logs)
                }
                if (data && data._lrd && data._lrd.memory) {
                    setServerMemory(data._lrd.memory)
                }
                if (data && data._lrd && data._lrd.models && data._lrd.modelsTimeline) {
                    setModelsData({
                        modelsSummary: data._lrd.models,
                        modelsTimeline: data._lrd.modelsTimeline
                    })
                }
                if (isJson) {
                    if (wasDataWrapped && data?.data) {
                        setResponseData(JSON.stringify(data?.data, null, 2))
                    } else {
                        setResponseData(JSON.stringify(data, null, 2))
                    }

                } else {
                    setResponseData(dataString)
                }

                setActiveTab('response')
            }).catch((error) => {
                setError("Response error: " + error)
                setResponseStatus(500)
                setSendingRequest(false)
                setActiveTab('response')
            })

    }

    useEffect(() => {
        const jsonAllParamsRegistry = JSON.parse(allParamsRegistry)
        let cached = ""
        if (jsonAllParamsRegistry[method + "-" + lrdDocsItem.uri]) {
            cached = jsonAllParamsRegistry[method + "-" + lrdDocsItem.uri]
        }

        if (method == 'GET' || method == 'HEAD' || method == 'DELETE') {
            if (cached && cached.trim() != "") {
                setQueryParams(cached)
                setCurlCommand(makeCurlCommand(host, lrdDocsItem.uri, method, cached, requestHeaders))
                return
            }
            let queries = ''
            let index = 0
            for (const [key] of Object.entries(lrdDocsItem.rules)) {
                index++
                const parts = key.split('.');
                const queryKey = parts.reduce((current: string, part: string) => {
                    return current ? `${current}[${part !== '*' ? part : 0}]` : part
                }, '')
                if (index == 1) {
                    queries += `?${queryKey}=\n`
                } else {
                    queries += `&${queryKey}=\n`
                }
            }
            setQueryParams(queries)

            setCurlCommand(makeCurlCommand(host, lrdDocsItem.uri, method, queries, requestHeaders))
        }
        if (method == 'POST' || method == 'PUT' || method == 'PATCH') {
            if (cached && (cached.trim() != "" || cached.trim() != "{}")) {
                setBodyParams(cached)
                setCurlCommand(makeCurlCommand(host, lrdDocsItem.uri, method, cached, requestHeaders))
                return
            }
            const body: any = Object.entries(lrdDocsItem.rules).reduce((acc, [key, rule]) => {
                if (rule.length == 0) {
                    return acc
                }

                const ruleObj = rule[0].split('|');

                if (ruleObj.includes('file') || ruleObj.includes('image')) {
                    return acc
                }

                const keys = key.split('.');
                keys.reduce((current: any, key, index) => {
                    key = key === "*" ? "0" : key;
                    if (index === keys.length - 1) {
                        if (!isNaN(Number(key))) {
                            current = !Array.isArray(current) ? [] : current;
                            return current
                        }
                        current[key] = ruleObj.includes('array') ? [] : "";
                    } else {
                        if (ruleObj.includes('array') || keys[index + 1] === "*" || !isNaN(Number(keys[index + 1]))) {
                            current[key] = current[key] || [];
                        } else {
                            current[key] = current[key] || {};
                        }
                    }
                    return current[key];
                }, acc);

                return acc;
            }, {})

            const jsonBody = JSON.stringify(body, null, 2)
            setBodyParams(jsonBody)
            setCurlCommand(makeCurlCommand(host, lrdDocsItem.uri, method, jsonBody, requestHeaders))
        }
    }, [])

    const handleChangeRequestHeaders = (value: any) => {
        setRequestHeaders(value)
        try {
            setError(null)
            JSON.parse(value)
        } catch (error: any) {
            setError("Global Request Headers are incorrect: " + error.message)
        }
    }

    return (
        <>
            <ApiActionTabs
                activeTab={activeTab}
                responseStatus={responseStatus}
                sqlQueriesCount={sqlQueriesCount}
                logsData={logsData}
                modelsData={modelsData}
                setActiveTab={setActiveTab} />

            <div className='mt-5'>
                {error && (
                    <div className="alert alert-error mt-2 mb-2">{error}</div>
                )}
                {activeTab == 'info' && (
                    <ApiActionInfo lrdDocsItem={lrdDocsItem} curlCommand={curlCommand} />
                )}
                {activeTab == 'request' && (
                    <ApiActionRequest
                        lrdDocsItem={lrdDocsItem}
                        requestUri={requestUri}
                        method={method}
                        sendingRequest={sendingRequest}
                        requestHeaders={requestHeaders}
                        bodyParams={bodyParams}
                        queryParams={queryParams}
                        setRequestUri={setRequestUri}
                        handleSendRequest={handleSendRequest}
                        handleChangeRequestHeaders={handleChangeRequestHeaders}
                        handleFileChange={handleFileChange}
                        setBodyParams={setBodyParams}
                        setQueryParams={setQueryParams} />
                )}

                {activeTab == 'response' && (
                    <ApiActionResponse
                        requestUri={requestUri}
                        method={method}
                        responseHeaders={responseHeaders}
                        responseData={responseData}
                        timeTaken={timeTaken}
                        responseStatus={responseStatus}
                        serverMemory={serverMemory} />
                )}
                {activeTab == 'sql' && (
                    <ApiActionSQL sqlData={sqlData} />
                )}

                {activeTab == 'logs' && (
                    <ApiActionLog logsData={logsData} />
                )}
                {activeTab == 'events' && (
                    <ApiActionEvents modelsData={modelsData} />
                )}
            </div>
        </>
    );
}
