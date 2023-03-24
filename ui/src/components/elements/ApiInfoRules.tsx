import React from 'react';
import { explode } from '../../libs/strings'
import shortid from 'shortid';
import { ChevronRightIcon, LinkIcon, EnvelopeIcon } from '@heroicons/react/24/outline'

interface Props {
    rules: string[],
    mainRule: string,
}

export default function ApiInfoRules(props: Props) {
    const { rules, mainRule } = props
    const StyledRule = (rule: any): JSX.Element => {
        const theRule = rule.rule
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
            <tr>
                <th className='param-cell'>
                    <span className='text-blue-500 pr-1'>¬</span>
                    <code className='pl-1'>
                        {mainRule}
                        {(mainRule.endsWith(".*")) ? (
                            <ChevronRightIcon key={shortid.generate()} className='inline-block w-4 h-4' />
                        ) : (<span key={shortid.generate()}></span>)}
                    </code>
                    {rules.map((rule) => (
                        rule.split('|').map((theRule) => (
                            (theRule == "file" || theRule == "image") ? (
                                <div key={shortid.generate()} className="block badge badge-success badge-outline ml-4 mt-1 mb-1 rounded-sm title">{theRule}</div>
                            ) : (<span key={shortid.generate()}></span>)
                        ))
                    ))}
                    {rules.map((rule) => (
                        rule.split('|').map((theRule) => (
                            (theRule == "required") ? (
                                <div className='block ml-6' key={shortid.generate()}>
                                    <code className='text-error font-normal'>{theRule}</code>
                                </div>
                            ) : (<span key={shortid.generate()}></span>)
                        ))
                    ))}
                    {rules.map((rule) => (
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
                    {rules.map((rule) => (
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
                    {rules.map((rule) => (
                        rule.split('|').map((theRule) => {
                            if (theRule == "required") {
                                return (<span key={shortid.generate()}></span>)
                            }
                            return (<span key={shortid.generate()}></span>)
                        })
                    ))}
                    {rules.map((rule) => (
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

        </>
    )
}
