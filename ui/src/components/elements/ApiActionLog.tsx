import React from 'react';
import "ace-builds";
import jsonWorkerUrl from 'ace-builds/src-min-noconflict/worker-json?url';
ace.config.setModuleUrl('ace/mode/json_worker', jsonWorkerUrl);

import AceEditor from 'react-ace';
import "ace-builds/src-noconflict/mode-sh";
import "ace-builds/src-noconflict/theme-one_dark";
import "ace-builds/src-noconflict/ext-language_tools";

interface Props {
    logsData: string,
}

export default function ApiActionLog(props: Props) {
    const { logsData } = props

    return (
        <>
            {!logsData && (
                <div className='text-center text-sm text-slate-500'>
                    No Laravel logs
                </div>
            )}
            {logsData && (
                <>
                    <p>Laravel logs</p>
                    <div className="rounded">
                        <AceEditor
                            maxLines={50}
                            width='100%'
                            mode="sh"
                            readOnly={true}
                            value={logsData}
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
    )
}
