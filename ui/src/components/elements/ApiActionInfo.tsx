import React from 'react';
import shortid from 'shortid';

import type { IAPIInfo } from '../../libs/types'
import { responsesText } from '../../libs/constants'
import ReactMarkdown from 'react-markdown'
import remarkGfm from 'remark-gfm'
import { ChevronRightIcon, CodeBracketIcon } from '@heroicons/react/24/outline';
import ApiActionCurl from './ApiActionCurl';

interface Props {
    lrdDocsItem: IAPIInfo,
    curlCommand: string,
}

export default function ApiActionInfo(props: Props) {
    const { lrdDocsItem, curlCommand } = props

    return (
        <div className="mockup-window border">
            <div className="p-5">
                {lrdDocsItem.doc_block && (
                    <div className='text-sm mb-10 text-slate-500'>
                        {/*  eslint-disable-next-line react/no-children-prop */}
                        <ReactMarkdown children={lrdDocsItem.doc_block} remarkPlugins={[remarkGfm]} />
                    </div>
                )}
                <table className="table table-fixed table-compact">
                    <tbody>
                        <tr>
                            <th>Method</th>
                            <td>
                                <span className={`method-${lrdDocsItem.http_method} uppercase`}>
                                    {lrdDocsItem.http_method}
                                </span>
                            </td>
                        </tr>
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
                                            <span className="badge badge-ghost badge-md mb-1 rounded-sm">{middleware}</span>
                                            <br />
                                        </div>
                                    ))}
                                </td>
                            </tr>
                        )}
                        <tr>
                            <th>
                                Status Codes
                            </th>
                            <td>
                                <div className="collapse">
                                    <input type="checkbox" />
                                    <div className="collapse-title text-sm text-slate-500 pl-0 mt-2">
                                        Show Response codes for this request
                                        <ChevronRightIcon className='inline-block w-4 h-4 ml-1' />
                                    </div>
                                    <div className="collapse-content p-0">
                                        {lrdDocsItem.responses && lrdDocsItem.responses.map((response) => (
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
                        <tr>
                            <th>
                                <CodeBracketIcon className='inline-block w-4 h-4 mr-1' />
                                Curl
                            </th>
                            <td>
                                <ApiActionCurl curlCommand={curlCommand} />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    )
}
