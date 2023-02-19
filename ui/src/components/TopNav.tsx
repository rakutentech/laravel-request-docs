import React, { useEffect } from 'react';

import useLocalStorage from 'react-use-localstorage';
import { Cog6ToothIcon, ArrowUpRightIcon, MoonIcon, SunIcon, XMarkIcon, ChatBubbleLeftIcon  } from '@heroicons/react/24/solid'

interface Props {
    handleChangeSettings: (
        showGet: string,
        showPost: string,
        showDelete: string,
        showPut: string,
        showPatch: string,
        showHead: string,
        sort: string,
        groupby: string) => void
    handleSearch: (search: string) => void
}
export default function TopNav(props: Props) {

    const { handleChangeSettings, handleSearch } = props
    const [theme, setTheme] = useLocalStorage('theme', '');
    const [sort, setSort] = useLocalStorage('sort', 'default');
    const [groupby, setGroupby] = useLocalStorage('groupby', 'default');
    const [showGet, setShowGet] = useLocalStorage('showGet', 'true');
    const [showPost, setShowPost] = useLocalStorage('showPost', 'true');
    const [showDelete, setShowDelete] = useLocalStorage('showDelete', 'true');
    const [showPut, setShowPut] = useLocalStorage('showPut', 'true');
    const [showPatch, setShowPatch] = useLocalStorage('showPatch', 'true');
    const [showHead, setShowHead] = useLocalStorage('showHead', 'true');

    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const handleChangeGroupby = (e: any) => {
        setGroupby(e.target.value)
        handleChangeSettings(showGet, showPost, showDelete, showPut, showPatch, showHead, sort, e.target.value)
    }
    const handleChangeSort = (e: any) => {
        setSort(e.target.value)
        handleChangeSettings(showGet, showPost, showDelete, showPut, showPatch, showHead, e.target.value, groupby)
    }
    const handleChangeGet = (e: any) => {
        setShowGet(e.target.checked)
        handleChangeSettings(e.target.checked, showPost, showDelete, showPut, showPatch, showHead, sort, groupby)
    }
    const handleChangePost = (e: any) => {
        setShowPost(e.target.checked)
        handleChangeSettings(showGet, e.target.checked, showDelete, showPut, showPatch, showHead, sort, groupby)
    }
    const handleChangeDelete = (e: any) => {
        setShowDelete(e.target.checked)
        handleChangeSettings(showGet, showPost, e.target.checked, showPut, showPatch, showHead, sort, groupby)
    }
    const handleChangePut = (e: any) => {
        setShowPut(e.target.checked)
        handleChangeSettings(showGet, showPost, showDelete, e.target.checked, showPatch, showHead, sort, groupby)
    }
    const handleChangePatch = (e: any) => {
        setShowPatch(e.target.checked)
        handleChangeSettings(showGet, showPost, showDelete, showPut, e.target.checked, showHead, sort, groupby)
    }
    const handleChangeHead = (e: any) => {
        setShowHead(e.target.checked)
        handleChangeSettings(showGet, showPost, showDelete, showPut, showPatch, e.target.checked, sort, groupby)
    }


    const toggleDarkMode = () => {
        const dataTheme = document.documentElement.getAttribute('data-theme');
        if (dataTheme === 'dark') {
            setTheme('light')
            document.documentElement.setAttribute('data-theme', 'light');
        } else {
            setTheme('dark')
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    }

    useEffect(() => {
        if (theme) {
            document.documentElement.setAttribute('data-theme', theme);
            return
        }
        const dataTheme = document.documentElement.getAttribute('data-theme');
        if (!dataTheme) {
            // check if dark mode is enabled for browser
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        }
        document.documentElement.setAttribute('data-theme', 'light');
    }, [])

    return (
        <header className="relative bg-none">
            <div className="navbar bg-base-200">
                <div className="flex-1">
                    <a className="btn btn-ghost normal-case text-xl">
                        <span className="pl-2">Laravel Request Docs</span>
                    </a>
                </div>
                <div className="flex-none">
                    <div className="form-control">
                        <input type="text" placeholder="Search" className="input input-sm input-bordered" onChange={ (e) => handleSearch(e.target.value) } />
                    </div>                    
                    <div className="menu menu-horizontal px-6 ">
                        <label className="swap swap-rotate">
                            <input type="checkbox" onChange={toggleDarkMode} />
                            {theme === 'dark' ? <SunIcon className="h-6 w-6"/> : <MoonIcon className="h-6 w-6"/>}
                        </label>
                    </div>
                    <div className="ml-1">
                        <a href="#modal-settings" className="btn btn-ghost btn-sm">
                            <span className="pr-1">
                                <Cog6ToothIcon className="h-6 w-6"/>
                            </span>
                        </a>
                        <div className="modal" id="modal-settings">
                            <div className="modal-box">
                                <h3 className="font-bold text-lg">Settings</h3>
                                <h4 className="font-bold mt-10">Sort By</h4>
                                <div className="form-control">
                                    <label className="label">
                                        
                                        <input type="radio" onChange={handleChangeSort} value="default" className="radio" checked={sort == "default"} />
                                        <span className="label-text">Default</span>
                                        
                                        <input type="radio" onChange={handleChangeSort} value="route_names" className="radio" checked={sort == "route_names"} />
                                        <span className="label-text">Routes</span>
                                        
                                        <input type="radio" onChange={handleChangeSort} value="method_names" className="radio" checked={sort == "method_names"} />
                                        <span className="label-text">HTTP Methods</span>
                                    </label>
                                </div>
                                {/* Not Implemented */}
                                {/* <h4 className="font-bold mt-10">Group By</h4>
                                <div className="form-control">
                                    <label className="label">
                                        
                                        <input type="radio" onChange={handleChangeGroupby} value="default" className="radio" checked={groupby == "default"} />
                                        <span className="label-text">Default</span>
                                        
                                        <input type="radio" onChange={handleChangeGroupby} value="controller_names" className="radio" checked={groupby == "controller_names"} />
                                        <span className="label-text">Controllers</span>
                                        
                                        <input type="radio" onChange={handleChangeGroupby} value="middleware_names" className="radio" checked={groupby == "middleware_names"} />
                                        <span className="label-text">Middlewares</span>
                                    </label>
                                </div> */}
                                <h4 className="font-bold mt-10">Display Settings</h4>
                                <div className="form-control">
                                    <label className="label">
                                        <span className="label-text">GET</span>
                                        <input type="checkbox" onChange={handleChangeGet} className="toggle toggle-success" checked={showGet == 'true'} />
                                    </label>
                                    <label className="label">
                                        <span className="label-text">POST</span>
                                        <input type="checkbox" onChange={handleChangePost} className="toggle toggle-success" checked={showPost == 'true'} />
                                    </label>
                                    <label className="label">
                                        <span className="label-text">DELETE</span>
                                        <input type="checkbox" onChange={handleChangeDelete} className="toggle toggle-success" checked={showDelete == 'true'} />
                                    </label>
                                    <label className="label">
                                        <span className="label-text">PUT</span>
                                        <input type="checkbox" onChange={handleChangePut} className="toggle toggle-success" checked={showPut == 'true'} />
                                    </label>
                                    <label className="label">
                                        <span className="label-text">PATCH</span>
                                        <input type="checkbox" onChange={handleChangePatch} className="toggle toggle-success" checked={showPatch == 'true'} />
                                    </label>
                                    <label className="label">
                                        <span className="label-text">HEAD</span>
                                        <input type="checkbox" onChange={handleChangeHead} className="toggle toggle-success" checked={showHead == 'true'} />
                                    </label>
                                </div>
                                <div className="modal-action">
                                    <a href="#" className="btn btn-sm">
                                        <XMarkIcon className="h-6 w-6"/> Close
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="ml-1 ">
                        <a className="btn btn-ghost btn-sm" href='/request-docs/api?openapi=true' target="_blank">
                            <span className="pr-1">
                                <ArrowUpRightIcon className="h-6 w-6"/>
                            </span>                             
                            OpenAPI 3.0
                        </a>
                    </div>
                    <div className="ml-1 ">
                        <a className="btn btn-ghost btn-sm" href='https://github.com/rakutentech/laravel-request-docs/issues/new' target="_blank" rel="noreferrer">
                            <span className="pr-1">
                            <ChatBubbleLeftIcon className="h-6 w-6"/>
                            </span>                             
                            Feature request
                        </a>
                    </div>
                </div>
            </div>
        </header>
    )

}

