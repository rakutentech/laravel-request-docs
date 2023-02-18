import React from 'react';
import shortid from 'shortid';

interface IAPIRule {
    [key: string]: string[];
}
interface IAPIInfo {
    uri: string;
    methods: string[];
    middlewares: string[];
    controller: string;
    controller_full_path: string;
    method: string;
    httpMethod: string;
    rules: IAPIRule;
    docBlock: string;
}

interface Props {
    lrdDocsItem: IAPIInfo,
    method: string,
}
export default function ApiInfo(props: Props) {

    const { lrdDocsItem, method } = props

    const explode = (str: string, maxLength: number) => {
        let buff = "";
        const numOfLines = Math.floor(str.length/maxLength);
        for(let i = 0; i<numOfLines+1; i++) {
            buff += str.substr(i*maxLength, maxLength); if(i !== numOfLines) { buff += "<br/>"; }
        }
        return buff;
    }    

    return (
        <>
            <h2 className='text-lg' id={method + lrdDocsItem.uri}>
                <span className={`badge badge-ghost rounded-none method-${method}`}>{method}</span>
                <span className='pl-5'>{lrdDocsItem.uri}</span>
            </h2>
            <h3 className='pt-4'>
                <span className='text-sm text-slate-500'>REQUEST SCHEMA</span>
                <code className='pl-2 text-xs'>application/json</code>
            </h3>
            <div className='pt-4'>

                <table className="table table-fixed table-compact w-full">
                    <tbody>
                        {lrdDocsItem.rules && Object.keys(lrdDocsItem.rules).map((key) => (

                            <tr key={shortid.generate()}>
                                <th className='param-cell'>
                                    ¬ <code className='pl-1'>{key}</code>
                                    {lrdDocsItem.rules[key].map((rule) => (
                                        rule.split('|').map((theRule) => (
                                            theRule == "required" ? (
                                                <div className='pl-6' key={shortid.generate()}>
                                                    <code className='text-error font-normal'>{theRule}</code>
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
                                                || theRule == "array") {
                                                return (<div key={shortid.generate()} className='capitalize text-slate-500'>{theRule}</div>)
                                            }
                                            return (<span key={shortid.generate()}></span>)
                                        })
                                    ))}
                                    {lrdDocsItem.rules[key].map((rule) => (
                                        rule.split('|').map((theRule) => {
                                            if (theRule == "required") {
                                                return (<span key={shortid.generate()}></span>)
                                            }
                                            if (theRule == "nullable") {
                                                return (<div key={shortid.generate()} className='capitalize text-slate-500'>{theRule}</div>)
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
                                                || theRule == "array" 
                                                || theRule == "nullable") {
                                                return (<span key={shortid.generate()}></span>)
                                            }
                                            return (<span key={shortid.generate()}>
                                                <div className='' dangerouslySetInnerHTML={{__html: explode(theRule, 50)}} />
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
