import React, { useState, useEffect } from 'react';

import type { IAPIInfo } from '../libs/types'
import Files from 'react-files'
import "ace-builds";
import shortid from 'shortid';
import jsonWorkerUrl from 'ace-builds/src-min-noconflict/worker-json?url';
ace.config.setModuleUrl('ace/mode/json_worker', jsonWorkerUrl);

import AceEditor from 'react-ace';
import "ace-builds/src-noconflict/mode-json";
import "ace-builds/src-noconflict/theme-one_dark";
import "ace-builds/src-noconflict/ext-language_tools";

import { PaperAirplaneIcon, ChevronRightIcon, ExclamationTriangleIcon, LockOpenIcon } from '@heroicons/react/24/outline'


interface Props {
    lrdDocsItem: IAPIInfo,
    requestUri: string,
    method: string,
    sendingRequest: boolean,
    requestHeaders: string,
    bodyParams: string,
    queryParams: string,
    setRequestUri: (requestUri: string) => void,
    handleSendRequest: () => void,
    handleChangeRequestHeaders: (requestHeaders: string) => void,
    handleFileChange: (files: any, file: any) => void,
    setBodyParams: (bodyParams: string) => void,
    setQueryParams: (queryParams: string) => void,
}

export default function ApiActionRequest(props: Props) {
    const {
        lrdDocsItem,
        requestUri,
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

    const [files, setFiles] = useState<any>([])
    const [uploadedFiles, setUploadedFiles] = useState<any>({})

    const handleFileUploaded = (files: any, file: any) => {
        const uf = { ...uploadedFiles }
        uf[file] = files
        setUploadedFiles(uf)
        handleFileChange(files, file)
    }

    useEffect(() => {
        //check if lrdDocsItem has rules
        const files: any = []
        for (const [key, rule] of Object.entries(lrdDocsItem.rules)) {
            if (rule.length == 0) {
                continue
            }
            const theRule = rule[0].split("|")
            if (theRule.includes('file') || theRule.includes('image')) {
                files.push(key)
            }
        }
        setFiles(files)
    }, [])

    return (
        <>
            <div className="form-control">
                <label className="input-group input-group-sm">
                    <span className={`method-${method} pr-1`}>{method}</span>
                    <input type="text" defaultValue={requestUri} onChange={(e) => setRequestUri(e.target.value)} placeholder="Type here" className="focus:outline-none input w-4/6 input-bordered input-sm mr-2" />
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
                {(files.length != 0 && (method == 'POST' || method == 'PUT' || method == 'DELETE')) && (
                    <div className='text-sm text-slate-500 p-0'>
                        <ExclamationTriangleIcon className='inline-block w-4 h-4 ml-1 text-yellow-500' />
                        &nbsp; This request requires a file upload. <br />
                        <LockOpenIcon className='inline-block w-4 h-4 ml-1 text-slate-500' />
                        &nbsp; Global headers will be overridden as <code>application/json</code> ⇢ <code>multipart/form-data</code>
                        <br />
                    </div>
                )}
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

            {(method == 'POST' || method == 'PUT' || method == 'PATCH') && (
                <div className="mockup-code">
                    <span className='pl-5 text-sm text-slate-500'>REQUEST BODY</span>
                    {files.map((file: string) =>
                        <div key={shortid.generate()}>
                            <Files
                                className='p-5 bg-gray-800 border border-gray-500 border-double hover:bg-gray-700 hover:border-dashed hover:cursor-pointer'
                                onChange={(e: any) => handleFileUploaded(e, file)}
                                multiple={file.includes('.*')}
                                maxFileSize={10000000}
                                minFileSize={0}
                                clickable
                            >
                                {uploadedFiles[file] && uploadedFiles[file].length > 0 && (
                                    <div className='text-sm text-gray-300'>
                                        {uploadedFiles[file].map((file: any, index: any) => (
                                            <div key={file.id}>
                                                {index + 1}) {file.name} - {file.size} bytes
                                            </div>
                                        ))}
                                    </div>
                                )}
                                <span className='text-slate-500'>
                                    <code>
                                        <small>{file}</small>
                                    </code>
                                    {file.includes('.*') && (
                                        <ChevronRightIcon className='inline-block w-4 h-4 ml-1' />
                                    )}
                                    <br />
                                    {file.includes('.*')
                                        ? 'Drop or click to upload multiple files'
                                        : 'Drop or click to upload single file'
                                    }
                                </span>
                            </Files>
                        </div>
                    )}
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
