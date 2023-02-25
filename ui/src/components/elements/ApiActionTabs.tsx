import React from 'react';

import { InformationCircleIcon, PaperAirplaneIcon, CircleStackIcon, DocumentTextIcon, ReceiptRefundIcon  } from '@heroicons/react/24/solid'

interface Props {
    responseStatus: number,
    activeTab: string,
    sqlQueriesCount: number,
    logData: string,
    setActiveTab: (tab: string) => void
}

export default function ApiActionTabs(props: Props) {
    const { activeTab, responseStatus, sqlQueriesCount, logData, setActiveTab } = props

    return (
        <div className="tabs tabs-boxed">
            <a className={`tab ${activeTab == 'info' ? 'tab-active' : ''}`} onClick={() => setActiveTab('info')}>
                <InformationCircleIcon className='inline-block w-5 h-5 mr-1' /> Info
            </a>
            <a className={`tab ${activeTab == 'request' ? 'tab-active' : ''}`} onClick={() => setActiveTab('request')}>
                <PaperAirplaneIcon className='inline-block w-5 h-5 mr-1' /> Request
            </a>
            <a className={`tab ${activeTab == 'response' ? 'tab-active' : ''}`} onClick={() => setActiveTab('response')}>
                <ReceiptRefundIcon className='inline-block w-5 h-5 mr-1' /> Response
                {responseStatus != 0 && (
                    <div className={`ml-1 badge badge-sm badge-${responseStatus} badge-info`}>{responseStatus}</div>
                )}
            </a>
            <a className={`tab ${activeTab == 'sql' ? 'tab-active' : ''}`} onClick={() => setActiveTab('sql')}>
                <CircleStackIcon className='inline-block w-5 h-5 mr-1' /> SQL
                {responseStatus != 0 && (
                    <div className="ml-1 badge badge-sm badge-warning">
                        {sqlQueriesCount}
                    </div>
                )}
            </a>
            <a className={`tab ${activeTab == 'log' ? 'tab-active' : ''}`} onClick={() => setActiveTab('logs')}>
                <DocumentTextIcon className='inline-block w-5 h-5 mr-1' /> Logs
                {responseStatus != 0 && (
                    <div className="ml-1 badge badge-sm badge-info">
                        {logData.split("\n").length - 1}
                    </div>
                )}
            </a>
        </div>
    )
}
