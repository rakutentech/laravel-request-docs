
import React, { useEffect, useState } from 'react';
import TopNav from "./TopNav"
import Sidebar from './Sidebar';
import ApiInfo from './ApiInfo';
import ApiAction from './ApiAction';
import useLocalStorage from 'react-use-localstorage';
import shortid from 'shortid';
import Fuse from 'fuse.js';
import type { IAPIInfo } from '../libs/types'


export default function App() {

    const [lrdDocsJson, setLrdDocsJson] = useState<IAPIInfo[]>([]);
    const [lrdDocsJsonCopy, setLrdDocsJsonCopy] = useState<IAPIInfo[]>([]);
    const [apiURL, setApiURL] = useState<string>('');
    const [host, setHost] = useState<string>('');
    const [sendingRequest, setSendingRequest] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [groupby] = useLocalStorage('groupby', 'default');
    const [sort] = useLocalStorage('sort', 'default');
    const [showGet] = useLocalStorage('showGet', 'true');
    const [showPost] = useLocalStorage('showPost', 'true');
    const [showDelete] = useLocalStorage('showDelete', 'true');
    const [showPut] = useLocalStorage('showPut', 'true');
    const [showPatch] = useLocalStorage('showPatch', 'true');
    const [showHead] = useLocalStorage('showHead', 'true');

    const searchOptions = {
        keys: ['uri', 'doc_block'],
        threshold: 0.3
    };

    const getUrl = (url: string, showGet: string, showPost: string, showDelete: string, showPut: string, showPatch: string, showHead: string, sort: string, groupby: string) => {
        return `${url}?json=true&showGet=${showGet}&showPost=${showPost}&showDelete=${showDelete}&showPut=${showPut}&showPatch=${showPatch}&showHead=${showHead}&sort=${sort}&groupby=${groupby}`
    }

    useEffect(() => {
        // get query param named api
        const urlParams = new URLSearchParams(window.location.search);
        let url = urlParams.get('api');

        if (!url) {
            // get current url without query params
            const domain = location.protocol + '//' + location.host
            setHost(domain)
            url = domain + "/request-docs/api"
        }

        if (url) {
            // extract host from url
            const domain = url?.split('/').slice(0, 3).join('/');
            setHost(domain)
        }
        setApiURL(url)

        const api = getUrl(url, showGet, showPost, showDelete, showPut, showPatch, showHead, sort, groupby)
        generateDocs(api)
    }, [])

    const scrollToAnchorOnHistory = () => {
        // get the anchor link and scroll to it
        const anchor = window.location.hash;
        if (anchor) {
            const anchorId = anchor.replace('#', '');
            const element = document.getElementById(anchorId);
            if (element) {
                element.scrollIntoView();
            }
        }
    }

    const generateDocs = (url: string) => {
        setSendingRequest(true)
        const response = fetch(url);
        response
            .then(lrdDocsJson => lrdDocsJson.json())
            .then((lrdDocsJson) => {
                // check if not an array
                if (!Array.isArray(lrdDocsJson)) {
                    setError("Invalid response")
                    setSendingRequest(false)
                    return
                }
                setError(null)
                setLrdDocsJson(lrdDocsJson)
                setLrdDocsJsonCopy(lrdDocsJson)
                setSendingRequest(false)
                setTimeout(() => {
                    scrollToAnchorOnHistory()
                }, 10) // greater than 1 is fine
            }).catch((error) => {
                setError(error.message)
                setSendingRequest(false)
            })
    }

    const handleSearch = (search: string) => {
        search = search.trim()
        if (!search) {
            setLrdDocsJson(lrdDocsJsonCopy)
            return
        }
        const fuse = new Fuse(lrdDocsJson, searchOptions);

        const filteredData = fuse.search(search);
        const filteredLrdJson: IAPIInfo[] = []
        for (let i = 0; i < filteredData.length; i++) {
            filteredLrdJson.push(filteredData[i].item)
        }

        setLrdDocsJson(filteredLrdJson)
    }

    const handleChangeSettings = (showGet: string,
        showPost: string,
        showDelete: string,
        showPut: string,
        showPatch: string,
        showHead: string,
        sort: string,
        groupby: string) => {
        const url = getUrl(apiURL, showGet, showPost, showDelete, showPut, showPatch, showHead, sort, groupby)
        generateDocs(url)
    }
    return (
        <>
            <div className="sticky top-0 z-50 bg-gray-400">
                <TopNav handleChangeSettings={handleChangeSettings} handleSearch={handleSearch} />
                {sendingRequest && (
                    <progress className="progress progress-success w-full"></progress>
                )}
                {!sendingRequest && (
                    <progress className="progress w-full" value="100"></progress>
                )}
                {error && (
                    <div className="alert alert-error rounded-none">
                        {error}
                    </div>
                )}
            </div>
            <div className="main-grid grid grid-cols-10 gap-2">
                <div className="sidebar-wrapper col-span-3">
                    <div className='min-h-screen'>
                        <Sidebar lrdDocsJson={lrdDocsJson} />
                    </div>
                </div>
                <div className="pt-10 col-span-7">
                    {lrdDocsJson.map((lrdDocsItem) => (
                        <div key={shortid.generate()}>
                            <div className="min-h-screen">
                                <div className="main-grid grid grid-cols-10 gap-2">
                                    <div className="col-span-4 ml-5">
                                        <ApiInfo lrdDocsItem={lrdDocsItem} method={lrdDocsItem.http_method} />
                                    </div>
                                    <div className="col-span-5 ml-5">
                                        <ApiAction lrdDocsItem={lrdDocsItem} method={lrdDocsItem.http_method} host={host} />
                                    </div>
                                </div>
                            </div>
                            <div className="divider"></div>
                        </div>
                    ))}
                </div>
            </div>
        </>
    );
}
