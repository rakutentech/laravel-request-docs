import React from 'react';
import AnchorLink from 'react-anchor-link-smooth-scroll'
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
    lrdDocsJson: IAPIInfo[],
}
export default function Sidebar(props: Props) {

    const { lrdDocsJson } = props

    return (
        <>
            <aside>
                <h2 className="title pl-5 pt-2 mb-5">
                    API List  <span className='text-slate-500 capitalize'>Total {lrdDocsJson.length}</span>
                </h2>
                <ul>
                    {lrdDocsJson.map((lrdDocsItem) => (
                        lrdDocsItem.methods.map((method) => (
                            <li key={shortid.generate()}>
                                <AnchorLink href={'#' + method + lrdDocsItem.uri} className="flex flex-row px-0 py-1">
                                    <span className={`method-${method} uppercase text-xs w-12 p-0 flex flex-row-reverse`}>
                                        {method}
                                    </span>
                                    <span className="flex-1 p-0 text-sm text-left break-all">
                                        {lrdDocsItem.uri}
                                    </span>
                                </AnchorLink>
                            </li>
                        ))
                    ))}
                    <li className='bg-transparent'></li>
                    <li className='bg-transparent'></li>
                    <li className='bg-transparent'></li>
                    <li className='bg-transparent'></li>
                    <li className='bg-transparent'></li>
                    <li className='bg-transparent'></li>
                    <li className='bg-transparent'></li>
                    <li className='bg-transparent'></li>
                    <li className='bg-transparent'></li>
                    <li className='bg-transparent'></li>
                </ul>
            </aside>
        </>
    );
}
