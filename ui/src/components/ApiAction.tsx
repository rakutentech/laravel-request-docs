import React, { useEffect, useState } from 'react';

import ReactMarkdown from 'react-markdown'
import remarkGfm from 'remark-gfm'

import "ace-builds";
import jsonWorkerUrl from 'ace-builds/src-min-noconflict/worker-json?url';
ace.config.setModuleUrl('ace/mode/json_worker', jsonWorkerUrl);

import AceEditor from 'react-ace';
import "ace-builds/src-noconflict/mode-json";
import "ace-builds/src-noconflict/mode-sql";
import "ace-builds/src-noconflict/mode-sh";
import "ace-builds/src-noconflict/theme-one_dark";
import "ace-builds/src-noconflict/ext-language_tools";

import useLocalStorage from 'react-use-localstorage';
import shortid from 'shortid';

interface IAPIRule {
    [key: string]: string[];
}
interface IAPIInfo {
    uri: string;
    methods: string[];
    middlewares: string[];
    controller: string;
    controller_full_path: string;
    method: string;
    httpMethod: string;
    rules: IAPIRule;
    docBlock: string;
}

interface Props {
    lrdDocsItem: IAPIInfo,
    method: string,
    host: string,
    allParamsRegistry: string,
    setAllParamsRegistery: (value: string) => void
}
export default function ApiAction(props: Props) {
    const { lrdDocsItem, method, host, allParamsRegistry, setAllParamsRegistery } = props
    const [error, setError] = useState<string | null>(null);
    const defaultHeaders = `{
  "Content-Type": "application/json",
  "Accept": "application/json"
}`
    const [requestHeaders, setRequestHeaders] = useLocalStorage('requestHeaders', defaultHeaders);
    const [curlCommand, setCurlCommand] = useState("");
    const [requestUri, setRequestUri] = useState(lrdDocsItem.uri);
    const [timeTaken, setTimeTaken] = useState(0);
    const [sendingRequest, setSendingRequest] = useState(false);
    const [queryParams, setQueryParams] = useState('');
    const [bodyParams, setBodyParams] = useState('');
    const [showingInfo, setShowingInfo] = useState(true);
    const [showingRequest, setShowingRequest] = useState(false);
    const [showingResponse, setShowingResponse] = useState(false);
    const [showingSQL, setShowingSQL] = useState(false);
    const [showingLog, setShowingLog] = useState(false);
    const [responseData, setResponseData] = useState("");
    const [sqlQueriesCount, setSqlQueriesCount] = useState(0);
    const [sqlData, setSqlData] = useState("");
    const [logData, setLogData] = useState("");
    const [serverMemory, setServerMemory] = useState("");
    const [responseStatus, setResponseStatus] = useState(0);
    const [responseHeaders, setResponseHeaders] = useState("");
    const [activeTab, setActiveTab] = useState('info');

    const setGetCurlCommand = (queries: string) => {
        let curl = `curl -X ${method} "${host}/${lrdDocsItem.uri}${queries}"`

        try {
            const jsonRequestHeaders = JSON.parse(requestHeaders)
            for (const [key, value] of Object.entries(jsonRequestHeaders)) {
                curl += ` -H "${key}: ${value}"`
            }
        } catch (error: any) {
            curl += ` -H "Content-Type: application/json"`
        }

        setCurlCommand(curl)
    }
    const setPostCurlCommand = (jsonBody: string) => {
        let curl = `curl -X ${method} "${host}/${lrdDocsItem.uri}" -d '${jsonBody}'`
        try {
            const jsonRequestHeaders = JSON.parse(requestHeaders)
            for (const [key, value] of Object.entries(jsonRequestHeaders)) {
                curl += ` -H "${key}: ${value}"`
            }
        } catch (error: any) {
            curl += ` -H "Content-Type: application/json"`
        }
        setCurlCommand(curl)
    }

    const handleSendRequest = () => {
        // update localstorage
        const jsonAllParamsRegistry = JSON.parse(allParamsRegistry)
        if (method == 'GET' || method == 'HEAD' || method == 'DELETE') {
            jsonAllParamsRegistry[method + "-" + lrdDocsItem.uri] = queryParams
        }
        if (method == 'POST' || method == 'PUT' || method == 'PATCH') {
            jsonAllParamsRegistry[method + "-" + lrdDocsItem.uri] = bodyParams
        }

        setAllParamsRegistery(JSON.stringify(jsonAllParamsRegistry))

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

        fetch(`${host}/${requestUri}${queryParams}`, options).then((response) => {
            let timeTaken = performance.now() - startTime
            // round to 3 decimals
            timeTaken = Math.round((timeTaken + Number.EPSILON) * 1000) / 1000
            setTimeTaken(timeTaken)
            setResponseStatus(response.status)
            setResponseHeaders(JSON.stringify(Object.fromEntries(response.headers), null, 2))
            showResponse()
            setSendingRequest(false)
            return response.json();
        }).then((data) => {

            if (data && data._lrd && data._lrd.queries) {
                const sqlQueries = data._lrd.queries.map((query: any) => {
                    return "Connection: " + query.connection_name + " Time taken: " + query.time + "ms: \n" + query.sql + "\n"
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
        }).catch((error) => {
            setError("Response error: " + error)
            setResponseStatus(500)
            setSendingRequest(false)
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
                setGetCurlCommand(cached)
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

            setGetCurlCommand(queries)
        }
        if (method == 'POST' || method == 'PUT' || method == 'PATCH') {
            if (cached) {
                setBodyParams(cached)
                setPostCurlCommand(cached)
                return
            }
            const body: any = {}
            for (const [key] of Object.entries(lrdDocsItem.rules)) {
                body[key] = ""
            }
            const jsonBody = JSON.stringify(body, null, 2)
            setBodyParams(jsonBody)
            setPostCurlCommand(jsonBody)

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

    const showInfo = () => {
        setShowingRequest(false)
        setShowingResponse(false)
        setShowingSQL(false)
        setShowingInfo(!showingInfo)
        setShowingLog(false)
        setActiveTab('info')
    }
    const showRequest = () => {
        setShowingResponse(false)
        setShowingSQL(false)
        setShowingInfo(false)
        setShowingRequest(!showingRequest)
        setShowingLog(false)
        setActiveTab('request')
    }
    const showResponse = () => {
        setShowingRequest(false)
        setShowingSQL(false)
        setShowingInfo(false)
        setShowingResponse(!showingResponse)
        setShowingLog(false)
        setActiveTab('response')
    }
    const showSQL = () => {
        setShowingRequest(false)
        setShowingResponse(false)
        setShowingInfo(false)
        setShowingSQL(!showingSQL)
        setShowingLog(false)
        setActiveTab('sql')
    }
    const showLog = () => {
        setShowingRequest(false)
        setShowingResponse(false)
        setShowingInfo(false)
        setShowingSQL(false)
        setShowingLog(!showingLog)
        setActiveTab('log')
    }

    return (
        <>

            <div className="tabs tabs-boxed">
                <a className={`tab ${activeTab == 'info' ? 'tab-active' : ''}`} onClick={showInfo}>Info</a>
                <a className={`tab ${activeTab == 'request' ? 'tab-active' : ''}`} onClick={showRequest}>Request</a>
                <a className={`tab ${activeTab == 'response' ? 'tab-active' : ''}`} onClick={showResponse}>
                    Response
                    {responseStatus != 0 && (
                        <div className={`ml-1 badge badge-sm badge-${responseStatus} badge-info`}>{responseStatus}</div>
                    )}
                </a>
                <a className={`tab ${activeTab == 'sql' ? 'tab-active' : ''}`} onClick={showSQL}>
                    SQL
                    {responseStatus != 0 && (
                        <div className="ml-1 badge badge-sm badge-warning">
                            {sqlQueriesCount} queries
                        </div>
                    )}
                </a>
                <a className={`tab ${activeTab == 'log' ? 'tab-active' : ''}`} onClick={showLog}>
                    Logs
                    {responseStatus != 0 && (
                        <div className="ml-1 badge badge-sm badge-info">
                            {logData.split("\n").length - 1} lines
                        </div>
                    )}
                </a>
            </div>

            <div className='mt-5'>
                {error && (
                    <div className="alert alert-error mt-2 mb-2">
                        {error}
                    </div>
                )}
                {showingInfo && (
                    <>
                        <div className="mockup-window border">
                            <div className="p-5">
                                <div className='text-sm'>
                                    {/*  eslint-disable-next-line react/no-children-prop */}
                                    <ReactMarkdown children={lrdDocsItem.docBlock} remarkPlugins={[remarkGfm]} />
                                </div>
                                <table className="table table-fixed table-compact">
                                    <tbody>
                                        {lrdDocsItem.controller && (
                                        <tr>
                                            <th>Controller</th>
                                            <td>{lrdDocsItem.controller}</td>
                                        </tr>
                                        )}
                                        {lrdDocsItem.method && (
                                        <tr>
                                            <th>Function</th>
                                            <td>{lrdDocsItem.method}</td>
                                        </tr>
                                        )}
                                        {lrdDocsItem.middlewares.length != 0 && (
                                        <tr>
                                            <th>Middlewares</th>
                                            <td>
                                                {lrdDocsItem.middlewares.map((middleware) => (
                                                    <div key={shortid.generate()}>
                                                        <span className="ml-1 badge badge-normal badge-sm">{middleware}</span>
                                                        <br />
                                                    </div>
                                                ))}
                                            </td>
                                        </tr>
                                        )}
                                        <tr>
                                            <th>Curl</th>
                                            <td>
                                                <small>
                                                    <pre className='p-2 bg-base-300'>{curlCommand}</pre>
                                                </small>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </>
                )}
                {showingRequest && (
                    <>
                        <div className="form-control">
                            <label className="input-group input-group-sm">
                                <span className={`method-${method}`}>{method}</span>
                                <input type="text" defaultValue={requestUri} onChange={setRequestUri} placeholder="Type here" className="focus:outline-none input w-full input-bordered input-sm" />
                                <button className="btn btn-sm btn-success" onClick={handleSendRequest} disabled={sendingRequest}>
                                    GO
                                </button>
                                <br />

                            </label>
                            {sendingRequest && (
                                <progress className="progress progress-success w-full"></progress>
                            )}
                        </div>
                        <br />

                        <div className="collapse collapse-arrow">
                            <input type="checkbox" />
                            <div className="collapse-title text-sm text-slate-500 pl-0">
                                Set Global Headers
                            </div>
                            <div className="collapse-content p-0">
                                <AceEditor
                                    height='200px'
                                    width='100%'
                                    mode="json"
                                    value={requestHeaders}
                                    onChange={handleChangeRequestHeaders}
                                    theme="one_dark"
                                    onLoad={function (editor) { editor.renderer.setPadding(0); editor.renderer.setScrollMargin(5, 5, 5, 5); editor.renderer.setShowPrintMargin(false); editor.setFontSize(14) }}
                                    editorProps={{
                                        $blockScrolling: true
                                    }}
                                />
                            </div>
                        </div>
                        <br />

                        {(method == 'GET' || method == 'HEAD' || method == 'DELETE') && (
                            <div className="mockup-code">
                                <span className='pl-5 text-sm text-slate-500'>
                                    Query Params. Example <code>?abc=123&def=456</code>
                                </span>
                                <AceEditor
                                    height='200px'
                                    width='100%'
                                    mode="sql"
                                    wrapEnabled={true}
                                    value={queryParams}
                                    onChange={setQueryParams}
                                    theme="one_dark"
                                    onLoad={function (editor) { editor.renderer.setPadding(0); editor.renderer.setScrollMargin(5, 5, 5, 5); editor.renderer.setShowPrintMargin(false); editor.setFontSize(14) }}
                                    editorProps={{
                                        $blockScrolling: true
                                    }}
                                />
                            </div>
                        )}
                        {(method == 'POST' || method == 'PUT' || method == 'PATH') && (
                            <div className="mockup-code">
                                <span className='pl-5 text-sm text-slate-500'>REQUEST BODY</span>
                                <AceEditor
                                    height='200px'
                                    width='100%'
                                    mode="json"
                                    wrapEnabled={true}
                                    value={bodyParams}
                                    onChange={setBodyParams}
                                    theme="one_dark"
                                    onLoad={function (editor) { editor.renderer.setPadding(0); editor.renderer.setScrollMargin(5, 5, 5, 5); editor.renderer.setShowPrintMargin(false); editor.setFontSize(14) }}
                                    editorProps={{
                                        $blockScrolling: true
                                    }}
                                />
                            </div>
                        )}
                    </>
                )}

                {showingResponse && (
                    <>
                        {responseHeaders && (
                            <>
                                <div className="collapse collapse-arrow">
                                    <input type="checkbox" />
                                    <div className="collapse-title text-sm text-slate-500 pl-0">
                                        Show Response Headers
                                    </div>
                                    <div className="collapse-content p-0">
                                        <AceEditor
                                            maxLines={35}
                                            width='100%'
                                            mode="json"
                                            wrapEnabled={true}
                                            value={responseHeaders}
                                            theme="one_dark"
                                            onLoad={function (editor) { editor.renderer.setPadding(0); editor.renderer.setScrollMargin(5, 5, 5, 5); editor.renderer.setShowPrintMargin(false); }}
                                            editorProps={{
                                                $blockScrolling: true
                                            }}
                                        />
                                    </div>
                                </div>
                                <br />
                            </>
                        )}
                        {!responseData && (
                            <div className='text-center text-sm text-slate-500'>
                                No Response Data
                            </div>
                        )}
                        {responseData && (
                            <div className="mockup-code">
                                <span className='pl-5 text-sm'>Response. Took: <b>{timeTaken}ms</b>, Status Code: <b>{responseStatus}</b>, Server memory: <b>{serverMemory}</b></span>
                                <AceEditor
                                    maxLines={50}
                                    width='100%'
                                    mode="json"
                                    wrapEnabled={true}
                                    value={responseData}
                                    theme="one_dark"
                                    onLoad={function (editor) { editor.renderer.setPadding(0); editor.renderer.setScrollMargin(5, 5, 5, 5); editor.renderer.setShowPrintMargin(false); }}
                                    editorProps={{
                                        $blockScrolling: true
                                    }}
                                />
                            </div>
                        )}
                    </>
                )}
                {showingSQL && (
                    <>
                        {!sqlData && (
                            <div className='text-center text-sm text-slate-500'>
                                No SQL queries recorded
                            </div>
                        )}
                        {sqlData && (
                            <>
                                <p>SQL queries</p>
                                <div className='rounded'>
                                    <AceEditor
                                        maxLines={50}
                                        width='100%'
                                        mode="sql"
                                        wrapEnabled={true}
                                        value={sqlData}
                                        theme="one_dark"
                                        onLoad={function (editor) { editor.renderer.setPadding(0); editor.renderer.setScrollMargin(5, 5, 5, 5); editor.renderer.setShowPrintMargin(false); }}
                                        editorProps={{
                                            $blockScrolling: true
                                        }}
                                    />
                                </div>
                            </>
                        )}

                    </>
                )}

                {showingLog && (
                    <>
                        {!logData && (
                            <div className='text-center text-sm text-slate-500'>
                                No Laravel logs
                            </div>
                        )}
                        {logData && (
                            <>
                                <p>Laravel logs</p>
                                <div className="rounded">
                                    <AceEditor
                                        maxLines={50}
                                        width='100%'
                                        mode="sh"
                                        value={logData}
                                        theme="one_dark"
                                        wrapEnabled={true}
                                        onLoad={function (editor) { editor.renderer.setPadding(0); editor.renderer.setScrollMargin(5, 5, 5, 5); editor.renderer.setShowPrintMargin(false); }}
                                        editorProps={{
                                            $blockScrolling: true
                                        }}
                                    />
                                </div>
                            </>
                        )}

                    </>
                )}
            </div>
        </>
    );
}
