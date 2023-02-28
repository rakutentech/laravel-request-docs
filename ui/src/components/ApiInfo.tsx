import React, { useState, useEffect } from 'react';
import shortid from 'shortid';
import { explode } from '../libs/strings'
import type { IAPIInfo } from '../libs/types'
import { ChevronRightIcon, LinkIcon, EnvelopeIcon } from '@heroicons/react/24/outline'

interface Props {
    lrdDocsItem: IAPIInfo,
    method: string,
}
export default function ApiInfo(props: Props) {

    const { lrdDocsItem, method } = props

    const [hasFile, setHasFile] = useState(false)
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
        setHasFile(files.length > 0)
    }, [])

    const StyledRule = (theRule: any): JSX.Element => {
        theRule = theRule.rule
        const split = theRule.split(':')

        if (theRule == 'url') {
            return (
                <div className="block">
                    <LinkIcon className='inline-block w-4 h-4' />  {theRule}
                </div>
            )
        }
        if (theRule == 'email') {
            return (
                <div className="block">
                    <EnvelopeIcon className='inline-block w-4 h-4' />  {theRule}
                </div>
            )
        }

        if (split.length < 2) {
            return (
                <div className='' dangerouslySetInnerHTML={{ __html: explode(theRule, 50, "<br/>") }} />
            )
        }

        const keyPart = split[0]
        const valuePart = split.slice(1).join(' ')
        if (keyPart == 'max') {
            return (
                <div className="block badge badge-primary badge-outline mt-1 mb-1 rounded-sm">{`<= ${valuePart}`}</div>
            )
        }
        if (keyPart == 'min') {
            return (
                <div className="block badge badge-primary badge-outline mt-1 mb-1 rounded-sm">{`>= ${valuePart}`}</div>
            )
        }
        if (keyPart == 'date_format') {
            return (
                <div className="block badge badge-info badge-outline mt-1 mb-1 rounded-sm">
                    {`Format: ${valuePart}`}
                </div>
            )
        }
        if (keyPart == 'regex') {
            return (
                <>
                    <div className="inline-block badge badge-info badge-outline mt-1 mb-1 mr-2 rounded-sm">
                        Regexp
                    </div>
                    <code>${valuePart}</code>
                </>
            )
        }

        return (
            <div className='' dangerouslySetInnerHTML={{ __html: explode(theRule, 50, "<br/>") }} />
        )
    }

    return (
        <>
            <h2 className='text-lg' id={method + lrdDocsItem.uri}>
                <span className={`badge badge-ghost rounded-none method-${method}`}>{method}</span>
                <span className='pl-5'>{lrdDocsItem.uri}</span>
            </h2>
            <h3 className='pt-4'>
                <span className='text-sm text-slate-500'>REQUEST SCHEMA</span>
                <code className='pl-2 text-xs'>
                    {hasFile ? (
                        'multipart/form-data'
                    ) : (
                        'application/json'
                    )}
                </code>
            </h3>
            <div className='pt-4'>

                <table className="table table-fixed table-compact w-full">
                    <tbody>
                        {lrdDocsItem.rules && Object.keys(lrdDocsItem.rules).map((key) => (

                            <tr key={shortid.generate()}>
                                <th className='param-cell'>
                                    <span className='text-blue-500 pr-1'>¬</span>
                                    <code className='pl-1'>
                                        {key}
                                        {(key.endsWith(".*")) ? (
                                            <ChevronRightIcon key={shortid.generate()} className='inline-block w-4 h-4' />
                                        ) : (<span key={shortid.generate()}></span>)}
                                    </code>
                                    {lrdDocsItem.rules[key].map((rule) => (
                                        rule.split('|').map((theRule) => (
                                            (theRule == "file" || theRule == "image") ? (
                                                <div key={shortid.generate()} className="block badge badge-success badge-outline ml-4 mt-1 mb-1 rounded-sm title">{theRule}</div>
                                            ) : (<span key={shortid.generate()}></span>)
                                        ))
                                    ))}
                                    {lrdDocsItem.rules[key].map((rule) => (
                                        rule.split('|').map((theRule) => (
                                            (theRule == "required") ? (
                                                <div className='block ml-6' key={shortid.generate()}>
                                                    <code className='text-error font-normal'>{theRule}</code>
                                                </div>
                                            ) : (<span key={shortid.generate()}></span>)
                                        ))
                                    ))}
                                    {lrdDocsItem.rules[key].map((rule) => (
                                        rule.split('|').map((theRule) => (
                                            (theRule.startsWith("required_if")) ? (
                                                <div className='block ml-6' key={shortid.generate()}>
                                                    <code className='text-red-300 font-normal'>required_if</code>
                                                </div>
                                            ) : (<span key={shortid.generate()}></span>)
                                        ))
                                    ))}
                                </th>
                                <td>
                                    {lrdDocsItem.rules[key].map((rule) => (
                                        rule.split('|').map((theRule) => {
                                            if (theRule == "required") {
                                                return (<span key={shortid.generate()}></span>)
                                            }
                                            if (theRule == "integer"
                                                || theRule == "string"
                                                || theRule == "bool"
                                                || theRule == "date"
                                                || theRule == "file"
                                                || theRule == "image"
                                                || theRule == "array"
                                                || theRule == "nullable") {
                                                return (
                                                    <div key={shortid.generate()} className='capitalize text-slate-500'>
                                                        {theRule}
                                                    </div>)
                                            }
                                            return (<span key={shortid.generate()}></span>)
                                        })
                                    ))}
                                    {lrdDocsItem.rules[key].map((rule) => (
                                        rule.split('|').map((theRule) => {
                                            if (theRule == "required") {
                                                return (<span key={shortid.generate()}></span>)
                                            }
                                            return (<span key={shortid.generate()}></span>)
                                        })
                                    ))}
                                    {lrdDocsItem.rules[key].map((rule) => (
                                        rule.split('|').map((theRule) => {
                                            if (theRule == "required"
                                                || theRule == "integer"
                                                || theRule == "string"
                                                || theRule == "bool"
                                                || theRule == "date"
                                                || theRule == "file"
                                                || theRule == "image"
                                                || theRule == "array"
                                                || theRule == "nullable") {
                                                return (<span key={shortid.generate()}></span>)
                                            }
                                            return (<span key={shortid.generate()}>
                                                <StyledRule rule={theRule} />
                                            </span>)
                                        })
                                    ))}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </>
    );
}
