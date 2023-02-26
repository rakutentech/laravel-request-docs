import React, {useState} from 'react';
import "ace-builds";
import jsonWorkerUrl from 'ace-builds/src-min-noconflict/worker-json?url';
ace.config.setModuleUrl('ace/mode/json_worker', jsonWorkerUrl);

import AceEditor from 'react-ace';
import "ace-builds/src-noconflict/mode-sh";
import "ace-builds/src-noconflict/theme-one_dark";
import "ace-builds/src-noconflict/ext-language_tools";

import { ChevronRightIcon } from '@heroicons/react/24/outline';

interface Props {
    curlCommand: string,
}

export default function ApiActionCurl(props: Props) {
    const { curlCommand } = props
    const [showCurl, setShowCurl] = useState(false);

    return (
        <>
            <button className="text-sm text-slate-500" onClick={() => setShowCurl(!showCurl)}>
                Show curl command
                <ChevronRightIcon className='inline-block w-4 h-4 ml-1' />
            </button>        
            {showCurl && (
                <>
                    <div className="rounded mt-2">
                        <AceEditor
                            maxLines={50}
                            // width='100%'
                            mode="sh"
                            value={curlCommand}
                            theme="one_dark"
                            wrapEnabled={false}
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
