import React, { useEffect, useState } from 'react';
import TopNav from "./TopNav"
import Sidebar from './Sidebar';
import ApiInfo from './ApiInfo';
import ApiAction from './ApiAction';
import useLocalStorage from 'react-use-localstorage';
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
export default function App() {

    const [lrdDocsJson, setLrdDocsJson] = useState<IAPIInfo[]>([]);
    const [allParamsRegistry, setAllParamsRegistery] = useLocalStorage('allParamsRegistry', "{}");
    const [apiURL, setApiURL] = useState<string>('');
    const [host, setHost] = useState<string>('');
    const [sendingRequest, setSendingRequest] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [theme] = useLocalStorage('theme', '');
    const [groupby] = useLocalStorage('groupby', 'default');
    const [sort] = useLocalStorage('sort', 'default');
    const [showGet] = useLocalStorage('showGet', 'true');
    const [showPost] = useLocalStorage('showPost', 'true');
    const [showDelete] = useLocalStorage('showDelete', 'true');
    const [showPut] = useLocalStorage('showPut', 'true');
    const [showPatch] = useLocalStorage('showPatch', 'true');
    const [showHead] = useLocalStorage('showHead', 'true');



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

        const api = `${url}?json=true&showGet=${showGet}&showPost=${showPost}&showDelete=${showDelete}&showPut=${showPut}&showPatch=${showPatch}&showHead=${showHead}&theme=${theme}&sort=${sort}&groupby=${groupby}`        
        generateDocs(api)
    }, [])

    const generateDocs = (url: string) => {
        setSendingRequest(true)
        const response = fetch(url);
        response
            .then(lrdDocsJson => lrdDocsJson.json())
            .then((lrdDocsJson) => {
                setError(null)
                setLrdDocsJson(lrdDocsJson)
                setSendingRequest(false)
            }).catch((error) => {
                setError(error.message)
                setSendingRequest(false)
            })
    }

    const handleChangeSettings = (showGet: string,
        showPost: string,
        showDelete: string,
        showPut: string,
        showPatch: string,
        showHead: string,
        sort: string,
        groupby: string) => {
        const url = `${apiURL}?json=true&showGet=${showGet}&showPost=${showPost}&showDelete=${showDelete}&showPut=${showPut}&showPatch=${showPatch}&showHead=${showHead}&theme=${theme}&sort=${sort}&groupby=${groupby}`
        generateDocs(url)
    }
    return (
        <>
            <TopNav handleChangeSettings={handleChangeSettings} />
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
            <div className="main-grid grid grid-cols-10 gap-2">
                <div className="sidebar-wrapper col-span-3">
                    <div className='min-h-screen'>
                        <Sidebar lrdDocsJson={lrdDocsJson} />
                    </div>
                </div>
                <div className="pt-10 col-span-7">
                    {lrdDocsJson.map((lrdDocsItem) => (
                        lrdDocsItem.methods.map((method) => (
                            <div key={shortid.generate()}>
                                <div className="min-h-screen">
                                    <div className="main-grid grid grid-cols-10 gap-2">
                                        <div className="col-span-4 ml-5">
                                            <ApiInfo lrdDocsItem={lrdDocsItem} method={method}/>
                                        </div>
                                        <div className="col-span-5 ml-5">
                                            <ApiAction lrdDocsItem={lrdDocsItem} method={method} host={host} allParamsRegistry={allParamsRegistry} setAllParamsRegistery={setAllParamsRegistery}/>
                                        </div>
                                    </div>
                                </div>
                                <div className="divider"></div>
                            </div>
                        ))
                    ))}
                </div>
            </div>
        </>
    );
}
