import React from 'react';
import "ace-builds";
import jsonWorkerUrl from 'ace-builds/src-min-noconflict/worker-json?url';
ace.config.setModuleUrl('ace/mode/json_worker', jsonWorkerUrl);

import AceEditor from 'react-ace';
import "ace-builds/src-noconflict/mode-sql";
import "ace-builds/src-noconflict/theme-one_dark";
import "ace-builds/src-noconflict/ext-language_tools";


interface Props {
    sqlData: string,
}

export default function ApiActionSQL(props: Props) {
    const { sqlData } = props

    return (
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
                            readOnly={true}
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
    )
}
