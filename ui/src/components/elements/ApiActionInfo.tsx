import React from 'react';
import shortid from 'shortid';

import type { IAPIInfo } from '../../libs/types'
import { responsesText } from '../../libs/constants'
import { explode } from '../../libs/strings'
import ReactMarkdown from 'react-markdown'
import remarkGfm from 'remark-gfm'
import { ChevronRightIcon } from '@heroicons/react/24/solid';

interface Props {
    lrdDocsItem: IAPIInfo,
    curlCommand: string,
}

export default function ApiActionInfo(props: Props) {
    const { lrdDocsItem, curlCommand } = props

    return (
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
                            <th>Responses</th>
                            <td>
                                <div className="collapse">
                                    <input type="checkbox" />
                                    <div className="collapse-title text-sm text-slate-500 pl-0 mt-2">
                                        Show Response codes for this request
                                        <ChevronRightIcon className='inline-block w-4 h-4 ml-1' />
                                    </div>
                                    <div className="collapse-content p-0">
                                        {lrdDocsItem.responses.map((response) => (
                                            <div key={shortid.generate()}>
                                                <div className={`response response-${response}`}>
                                                    - {response} &nbsp; {responsesText[response]}
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </td>
                        </tr>
                        {/* Also not so beaufiul output */}
                        {/* <tr>
                            <th>Curl</th>
                            <td>
                                <small>
                                    <pre className='m-1 p-2 bg-base-300'>
                                        <div className='' dangerouslySetInnerHTML={{ __html: explode(curlCommand, 50, "\\<br/>") }} />
                                    </pre>
                                </small>
                            </td>
                        </tr> */}
                    </tbody>
                </table>
            </div>
        </div>
    )
}
