import React from 'react';

import { explode } from '../../libs/strings'

interface Props {
    modelsData: any,
}

export default function ApiActionEvents(props: Props) {
    const { modelsData } = props

    return (
        <>
            {!Object.keys(modelsData).length && (
                <div className='text-center text-sm text-slate-500'>
                    No Models Data
                </div>
            )}
            {Object.keys(modelsData).length != 0 && (
                <>
                    <h3 className='title'>Models</h3>
                    <p>
                        <small className='text-slate-500'>Events are in the order of occurances</small>
                    </p>
                    <div className='divider'></div>
                    {Object.keys(modelsData).map((model, index) => {
                        return (
                            <table className='table table-compact table-fixed table-zebra w-full mb-10' key={index}>
                                <tbody>
                                    {Object.keys(modelsData[model]).map((event, idx) => {
                                        return (
                                            <tr key={idx}>
                                                {idx == 0 && (
                                                    <td rowSpan={Object.keys(modelsData[model]).length}>
                                                        <span className='font-bold text-slate-500'>Model</span>
                                                        <br />
                                                        <div className='' dangerouslySetInnerHTML={{ __html: explode(model.split('\\')[model.split('\\').length - 1], 30, "<br/>") }} />
                                                    </td>
                                                )}
                                                <td className='capitalize'>{event}</td>
                                                <td>
                                                    <span className='font-bold'>{modelsData[model][event]}</span>
                                                    <span className='text-slate-400 pl-1'>
                                                        Time{modelsData[model][event] > 1 ? 's' : ''}
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
        </>
    )
}
