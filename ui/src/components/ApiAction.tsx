import React, { useEffect, useState } from 'react';

import useLocalStorage from 'react-use-localstorage';
import { defaultHeaders, makeCurlCommand } from '../libs/strings'
import type { IAPIInfo } from '../libs/types'
import ApiActionResponse from './elements/ApiActionResponse'
import ApiActionRequest from './elements/ApiActionRequest'
import ApiActionTabs from './elements/ApiActionTabs'
import ApiActionInfo from './elements/ApiActionInfo'
import ApiActionSQL from './elements/ApiActionSQL'
import ApiActionLog from './elements/ApiActionLog'

interface Props {
    lrdDocsItem: IAPIInfo,
    method: string,
    host: string
}
export default function ApiAction(props: Props) {
    const { lrdDocsItem, method, host } = props
    const [error, setError] = useState<string | null>(null);

    const [allParamsRegistry, setAllParamsRegistery] = useLocalStorage('allParamsRegistry', "{}");

    const [requestHeaders, setRequestHeaders] = useLocalStorage('requestHeaders', defaultHeaders);
    const [curlCommand, setCurlCommand] = useState("");
    const [requestUri, setRequestUri] = useState(lrdDocsItem.uri);
    const [timeTaken, setTimeTaken] = useState(0);
    const [sendingRequest, setSendingRequest] = useState(false);
    const [queryParams, setQueryParams] = useState('');
    const [bodyParams, setBodyParams] = useState('');
    const [responseData, setResponseData] = useState("");
    const [sqlQueriesCount, setSqlQueriesCount] = useState(0);
    const [sqlData, setSqlData] = useState("");
    const [logData, setLogData] = useState("");
    const [serverMemory, setServerMemory] = useState("");
    const [responseStatus, setResponseStatus] = useState(0);
    const [responseHeaders, setResponseHeaders] = useState("");
    const [activeTab, setActiveTab] = useState('info');

    const handleFileChange = (files: any) => {
        const bodyAppend = JSON.parse(bodyParams)
        bodyAppend["avatar"] = files[0]
        setBodyParams(JSON.stringify(bodyAppend))
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

        const options: any = {
            credentials: "include",
            method: method,
            headers: headers,
        }

        if (method == 'POST' || method == 'PUT' || method == 'PATCH') {
            try {
                JSON.parse(bodyParams)
            } catch (error: any) {
                setError("Request body incorrect: " + error.message)
                return
            }
            options['body'] = bodyParams
        }

        const startTime = performance.now();

        setSendingRequest(true)
        setSqlData("")
        setLogData("")
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
            return response.json();
        }).then((data) => {
            
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
                setLogData(logs)
            }
            if (data && data._lrd && data._lrd.memory) {
                setServerMemory(data._lrd.memory)
            }
            // remove key _lrd from response
            if (data && data._lrd) {
                delete data._lrd
            }
            setResponseData(JSON.stringify(data, null, 2))
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
            if (cached) {
                setQueryParams(cached)
                // setGetCurlCommand(cached)
                setCurlCommand(makeCurlCommand(host, lrdDocsItem.uri, method, cached, requestHeaders))
                return
            }
            let queries = ''
            let index = 0
            for (const [key] of Object.entries(lrdDocsItem.rules)) {
                index++
                if (index == 1) {
                    queries += `?${key}=\n`
                } else {
                    queries += `&${key}=\n`
                }
            }
            setQueryParams(queries)

            setCurlCommand(makeCurlCommand(host, lrdDocsItem.uri, method, queries, requestHeaders))
        }
        if (method == 'POST' || method == 'PUT' || method == 'PATCH') {
            if (cached) {
                setBodyParams(cached)
                setCurlCommand(makeCurlCommand(host, lrdDocsItem.uri, method, cached, requestHeaders))
                return
            }
            const body: any = {}
            for (const [key] of Object.entries(lrdDocsItem.rules)) {
                body[key] = ""
            }
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
                logData={logData}
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
                    <ApiActionLog logData={logData} />
                )}
            </div>
        </>
    );
}
