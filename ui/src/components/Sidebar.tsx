import React from 'react';
import AnchorLink from 'react-anchor-link-smooth-scroll'
import shortid from 'shortid';
import type { IAPIInfo } from '../libs/types'
import { ListBulletIcon  } from '@heroicons/react/24/solid'

interface Props {
    lrdDocsJson: IAPIInfo[],
}
export default function Sidebar(props: Props) {

    const { lrdDocsJson } = props

    return (
        <>
            <aside>
                <h2 className="title pl-5 pt-2 mb-5">
                    <ListBulletIcon className='inline-block w-4 h-4 mr-1' />
                    API List  <span className='text-slate-500 capitalize float-right mr-5'>Total {lrdDocsJson.length}</span>
                </h2>
                <ul>
                    {lrdDocsJson.map((lrdDocsItem) => (
                        <div key={shortid.generate()}>
                            {(lrdDocsItem.group != null && lrdDocsItem.group != "" && lrdDocsItem.group_index == 0) && (
                                <li className="pt-5 text-slate-600">
                                    {/* Only in case of controller names full path -> just controller name */}
                                    {lrdDocsItem.group.split('\\').pop()}
                                </li>
                            )}
                            <li>
                                <AnchorLink href={'#' + lrdDocsItem.http_method + lrdDocsItem.uri}
                                    offset={() => 120}
                                    onClick={() => {
                                        window.history.pushState({}, '', '#' + lrdDocsItem.http_method + lrdDocsItem.uri);
                                    }}
                                    className="flex flex-row px-0 py-1">
                                        <span className={`method-${lrdDocsItem.http_method} uppercase text-xs w-12 p-0 flex flex-row-reverse`}>
                                            {lrdDocsItem.http_method}
                                        </span>
                                    <span className="flex-1 p-0 text-sm text-left break-all">
                                            {lrdDocsItem.uri}
                                        </span>
                                </AnchorLink>
                            </li>
                        </div>
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
                </ul>
            </aside>
        </>
    );
}
