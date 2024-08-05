// noinspection t

import React, {useEffect, useState} from 'react';
import "ace-builds";
import jsonWorkerUrl from 'ace-builds/src-min-noconflict/worker-json?url';
import AceEditor from 'react-ace';
import "ace-builds/src-noconflict/mode-json";
import "ace-builds/src-noconflict/theme-one_dark";
import "ace-builds/src-noconflict/ext-language_tools";
import useLocalStorage from 'react-use-localstorage';

ace.config.setModuleUrl('ace/mode/json_worker', jsonWorkerUrl);

interface Props {
    responseData: string,
    responseHeaders: string,
    timeTaken: number,
    responseStatus: number,
    serverMemory: string,
    requestUri: string,
    method: string,
}

export default function ApiActionResponse(props: Props) {
    const { responseHeaders, responseData, timeTaken, responseStatus, serverMemory, requestUri, method } = props
    const [savePreviousResponse] = useLocalStorage('savePreviousResponse', 'false');
    const [previousResponse, setPreviousResponse] = useLocalStorage('previousResponse' + requestUri + method, '');
    const [isHtml, setIsHtml] = useState(false);
    useEffect(() => {
        if (JSON.parse(responseHeaders)['content-type'].split(';')[0] === 'text/html') {
            setIsHtml(true)
        }

        if (responseData && savePreviousResponse === 'true') {
            setPreviousResponse(responseData)
        }
    }, [])

    return (
        <>
            {responseHeaders && (
                <>
                    <div className="collapse collapse-arrow">
                        <input type="checkbox"/>
                        <div className="collapse-title text-sm text-slate-500 pl-0">
                            Show Response Headers
                        </div>
                        <div className="collapse-content p-0">
                            <AceEditor
                                maxLines={35}
                                readOnly={true}
                                width='100%'
                                mode="json"
                                wrapEnabled={true}
                                value={responseHeaders}
                                theme="one_dark"
                                onLoad={function (editor) {
                                    editor.renderer.setPadding(0);
                                    editor.renderer.setScrollMargin(5, 5, 5, 5);
                                    editor.renderer.setShowPrintMargin(false);
                                }}
                                editorProps={{
                                    $blockScrolling: true
                                }}
                            />
                        </div>
                    </div>
                    <br/>
                </>
            )}
            {(!responseData && !previousResponse) && (
                <div className='text-center text-sm text-slate-500'>
                    No Response Data
                </div>
            )}
            {(!responseData && previousResponse) && (
                <div className="mockup-code mb-5">
                    <span className='pl-5 text-sm text-warning'>Previous Response</span>
                    {! isHtml ? (
                        <AceEditor
                            maxLines={50}
                            width='100%'
                            mode="json"
                            wrapEnabled={true}
                            readOnly={true}
                            value={previousResponse}
                            theme="one_dark"
                            onLoad={function (editor) {
                                editor.renderer.setPadding(0);
                                editor.renderer.setScrollMargin(5, 5, 5, 5);
                                editor.renderer.setShowPrintMargin(false);
                            }}
                            editorProps={{
                                $blockScrolling: true
                            }}
                        />
                    ) : (<div dangerouslySetInnerHTML={{__html: responseData}} />)}
                </div>
            )}
            {responseData && (
                <div className="mockup-code">
                    <span className='pl-5 text-sm text-slate-500'>RESPONSE</span>
                    <br/>
                    <span
                        className='pl-5 text-sm'>Time taken: <b>{timeTaken}ms</b>, Status Code: <b>{responseStatus}</b>, Server memory: <b>{serverMemory}</b></span>
                    {! isHtml ? (
                        <AceEditor
                            maxLines={50}
                            width='100%'
                            mode="json"
                            wrapEnabled={true}
                            readOnly={true}
                            value={responseData}
                            theme="one_dark"
                            onLoad={function (editor) {
                                editor.renderer.setPadding(0);
                                editor.renderer.setScrollMargin(5, 5, 5, 5);
                                editor.renderer.setShowPrintMargin(false);
                            }}
                            editorProps={{
                                $blockScrolling: true
                            }}
                        />
                    ) : (<div dangerouslySetInnerHTML={{__html: responseData}} />)}
        </div>
    )
}
</>
)
}
