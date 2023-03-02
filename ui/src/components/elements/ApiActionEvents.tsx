import React from 'react';

import { explode } from '../../libs/strings'
import shortid from 'shortid';
import {
    CheckIcon,
    ChevronDoubleDownIcon,
    ArrowPathIcon,
    MinusIcon,
    PlusIcon,
} from '@heroicons/react/24/outline'

interface Props {
    modelsData: any,
}



export default function ApiActionEvents(props: Props) {
    const { modelsData } = props
    const { modelsSummary, modelsTimeline } = modelsData

    return (
        <>
            {!Object.keys(modelsSummary).length && (
                <div className='text-center text-sm text-slate-500'>
                    No Models Data
                </div>
            )}
            {Object.keys(modelsSummary).length != 0 && (
                <>
                    <h3 className='title'>Model events Summary</h3>
                    <div className='divider'></div>
                    {Object.keys(modelsSummary).map((model) => {
                        return (
                            <table className='table table-compact table-fixed table-zebra w-full mb-10' key={shortid.generate()}>
                                <tbody>
                                    {Object.keys(modelsSummary[model]).map((event, idx) => {
                                        return (
                                            <tr key={shortid.generate()}>
                                                {idx == 0 && (
                                                    <td rowSpan={Object.keys(modelsSummary[model]).length}>
                                                        <span className='font-bold text-slate-500'>Model</span>
                                                        <br />
                                                        <div className='' dangerouslySetInnerHTML={{ __html: explode(model.split('\\')[model.split('\\').length - 1], 30, "<br/>") }} />
                                                    </td>
                                                )}
                                                <td className='capitalize'>
                                                    {event == 'created' && <PlusIcon className={`inline-block w-3 h-3 mr-1`} />}
                                                    {event == 'saved' && <CheckIcon className={`inline-block w-3 h-3 mr-1`} />}
                                                    {event == 'retrieved' && <ChevronDoubleDownIcon className={`inline-block w-3 h-3 text-white mr-1`} />}
                                                    {event == 'updated' && <ArrowPathIcon className={`inline-block w-3 h-3 mr-1`} />}
                                                    {event == 'deleted' && <MinusIcon className={`inline-block w-3 h-3 mr-1`} />}
                                                    {event}
                                                </td>
                                                <td>
                                                    <span className='font-bold'>
                                                        {modelsSummary[model][event]}
                                                    </span>
                                                    <span className='text-slate-400 pl-1'>
                                                        Time{modelsSummary[model][event] > 1 ? 's' : ''}
                                                    </span>
                                                </td>
                                            </tr>
                                        )
                                    })}
                                </tbody>
                            </table>
                        )
                    })}
                </>
            )}

            {Object.keys(modelsTimeline).length != 0 && (
                <>
                    <h3 className='title'>Model Events Timeline</h3>
                    <small className='text-slate-500'>Events are in the order of occurrences</small>
                    <div className='divider'></div>
                    <div className="flex flex-col md:grid grid-cols-12">
                        {Object.keys(modelsTimeline).map((index) => {
                            return (
                                <div className="flex md:contents" key={shortid.generate()}>
                                    <div className="col-start-2 col-end-4 mr-10 md:mx-auto relative">
                                        <div className="h-full w-3 flex items-center justify-center">
                                            <div className={`h-full w-1 bg-${modelsTimeline[index].event} pointer-events-none`}></div>
                                        </div>
                                        <div className={`w-3 h-3 absolute top-1/2 rounded-full bg-${modelsTimeline[index].event} shadow text-center`}>
                                            {modelsTimeline[index].event == 'created' && <PlusIcon className={`w-3 h-3 text-black mx-auto`} />}
                                            {modelsTimeline[index].event == 'saved' && <CheckIcon className={`w-3 h-3 text-black mx-auto`} />}
                                            {modelsTimeline[index].event == 'retrieved' && <ChevronDoubleDownIcon className={`w-3 h-3 text-white mx-auto`} />}
                                            {modelsTimeline[index].event == 'updated' && <ArrowPathIcon className={`w-3 h-3 text-black mx-auto`} />}
                                            {modelsTimeline[index].event == 'deleted' && <MinusIcon className={`w-3 h-3 text-black mx-auto`} />}

                                        </div>
                                    </div>
                                    <div className="col-start-4 col-end-12 rounded-md my-3 mr-auto w-full break-all">
                                        <h3 className="mb-1">
                                            <div className='' dangerouslySetInnerHTML={{ __html: explode(modelsTimeline[index].model.split('\\')[modelsTimeline[index].model.split('\\').length - 1], 30, "<br/>") }} />
                                        </h3>
                                        <p className="title text-justify w-full">
                                            <span className={`badge badge-${modelsTimeline[index].event} rounded-sm`}>
                                                {modelsTimeline[index].event}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            )
                        })}
                    </div>
                </>
            )}
        </>
    )
}
