import React from 'react';

import {
    InformationCircleIcon,
    PaperAirplaneIcon,
    CircleStackIcon,
    DocumentTextIcon,
    TableCellsIcon,
    ReceiptRefundIcon
} from '@heroicons/react/24/outline'

interface Props {
    responseStatus: number,
    activeTab: string,
    sqlQueriesCount: number,
    logsData: string,
    modelsData: any,
    setActiveTab: (tab: string) => void
}

export default function ApiActionTabs(props: Props) {
    const { activeTab, responseStatus, sqlQueriesCount, logsData, modelsData, setActiveTab } = props

    return (
        <div className="tabs tabs-boxed">
            <a className={`tab ${activeTab == 'info' ? 'tab-active' : ''}`} onClick={() => setActiveTab('info')}>
                <InformationCircleIcon className='inline-block w-5 h-5' /> Info
            </a>
            <a className={`tab ${activeTab == 'request' ? 'tab-active' : 'text-success'}`} onClick={() => setActiveTab('request')}>
                <span className='font-bold'>
                    <PaperAirplaneIcon className='inline-block w-5 h-5' /> Send
                </span>
            </a>
            <a className={`pl-0 tab ${activeTab == 'response' ? 'tab-active' : ''}`} onClick={() => setActiveTab('response')}>
                <ReceiptRefundIcon className='inline-block w-5 h-5' /> Response
                {responseStatus != 0 && (
                    <div className={`ml-1 badge badge-sm badge-${responseStatus} badge-info`}>{responseStatus}</div>
                )}
            </a>
            <a className={`pl-0 pr-3 tab ${activeTab == 'sql' ? 'tab-active' : ''}`} onClick={() => setActiveTab('sql')}>
                <CircleStackIcon className='inline-block w-5 h-5 mr-1' /> SQL
                {responseStatus != 0 && (
                    <div className="ml-1 badge badge-xs badge-warning">
                        {sqlQueriesCount}
                    </div>
                )}
            </a>
            <a className={`pl-0 pr-3 tab ${activeTab == 'logs' ? 'tab-active' : ''}`} onClick={() => setActiveTab('logs')}>
                <DocumentTextIcon className='inline-block w-5 h-5 mr-1' /> Logs
                {responseStatus != 0 && (
                    <div className="ml-1 badge badge-xs badge-warning">
                        {logsData.split("\n").length - 1}
                    </div>
                )}
            </a>
            <a className={`pl-0 pr-1 tab ${activeTab == 'events' ? 'tab-active' : ''}`} onClick={() => setActiveTab('events')}>
                <TableCellsIcon className='inline-block w-5 h-5 mr-1' /> Events
                {responseStatus != 0 && (
                    <div className="ml-1 badge badge-xs badge-warning">
                        {(modelsData && modelsData.modelsSummary) ? Object.keys(modelsData.modelsSummary).length : 0}
                    </div>
                )}
            </a>
        </div>
    )
}
