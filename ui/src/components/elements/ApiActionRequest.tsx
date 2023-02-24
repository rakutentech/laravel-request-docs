import React from 'react';

import "ace-builds";
import jsonWorkerUrl from 'ace-builds/src-min-noconflict/worker-json?url';
ace.config.setModuleUrl('ace/mode/json_worker', jsonWorkerUrl);

import AceEditor from 'react-ace';
import "ace-builds/src-noconflict/mode-json";
import "ace-builds/src-noconflict/theme-one_dark";
import "ace-builds/src-noconflict/ext-language_tools";

import { PaperAirplaneIcon  } from '@heroicons/react/24/solid'


interface Props {
    requestUri: string,
    method: string,
    sendingRequest: boolean,
    requestHeaders: string,
    bodyParams: string,
    queryParams: string,
    setRequestUri: (requestUri: string) => void,
    handleSendRequest: () => void,
    handleChangeRequestHeaders: (requestHeaders: string) => void,
    handleFileChange: (e: any) => void,
    setBodyParams: (bodyParams: string) => void,
    setQueryParams: (queryParams: string) => void,
}

export default function ApiActionRequest(props: Props) {
    const { requestUri,
        method,
        sendingRequest,
        requestHeaders,
        bodyParams,
        queryParams,
        setRequestUri,
        handleSendRequest,
        handleChangeRequestHeaders,
        handleFileChange,
        setBodyParams,
        setQueryParams } = props

    return (
        <>
            <div className="form-control">
                <label className="input-group input-group-sm">
                    <span className={`method-${method}`}>{method}</span>
                    <input type="text" defaultValue={requestUri} onChange={(e) => setRequestUri(e.target.value)} placeholder="Type here" className="focus:outline-none input w-full input-bordered input-sm" />
                    <button className="btn btn-sm btn-success" onClick={handleSendRequest}>
                        GO <PaperAirplaneIcon className='inline-block w-4 h-4 ml-1' />
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
                    {/* <div className='pl-5'>Image</div>
                    <div className="files-dropzone">
                        <Files
                            className='files-dropzone'
                            onChange={handleFileChange}
                            multiple={true}
                            maxFileSize={10000000}
                            minFileSize={0}
                            clickable>
                            Drop files here or click to upload
                        </Files>
                    </div> */}
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
    )
}
