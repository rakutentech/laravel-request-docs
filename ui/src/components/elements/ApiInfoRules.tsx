import React from 'react';
import shortid from 'shortid';
import {InformationCircleIcon} from "@heroicons/react/24/outline";

interface Props {
    rules: string[],
    mainRule: string,
    infos?: { [key: string]: { description: string, example: string} },
    rules_order?: string[]
}

const orderingRules = (rule: string, rules_order: string[]): string => {
    if (!rules_order) {
        return rule;
    }
    const ruleArray = rule.split('|');
    const lastRules = ['min', 'max', 'nullable'];
    ruleArray.sort((a, b) => {
        const aKey = a.split(':')[0];
        const bKey = b.split(':')[0];
        let aIndex = rules_order.indexOf(aKey);
        let bIndex = rules_order.indexOf(bKey);

        const lastIndex = rules_order.length + 1;
        const defaultIndex = lastIndex / 2;

        aIndex = aIndex === -1 ? (lastRules.includes(aKey) ? lastIndex : defaultIndex) : aIndex;
        bIndex = bIndex === -1 ? (lastRules.includes(bKey) ? lastIndex : defaultIndex) : bIndex;

        return aIndex - bIndex;
    });
    return ruleArray.join('|');
}

const generateJSX = (rule: string, className: string): JSX.Element => {
    return (<code key={shortid.generate()} className={className}>{rule} </code>);
}

export default function ApiInfoRules(props: Props) {
    const { rules, mainRule, infos, rules_order } = props

    const FormatRules = (rules: string[]): JSX.Element => {
        const concatRules: React.JSX.Element[] = [];

        const ruleHandlers: { [key: string]: (rule: string, split: string[]) => void } = {
            "file": (rule: string) => concatRules.push(generateJSX(rule, 'text-success text-xs')),
            "image": (rule: string) => concatRules.push(generateJSX(rule, 'text-success text-xs')),
            "required": (rule: string) => concatRules.push(generateJSX(rule, 'text-error text-xs')),
            "required_if": () => concatRules.push(generateJSX('required_if', 'text-red-400 text-xs')),
            "max": (rule, split) => concatRules.push(generateJSX(`<=${split[1]}`, 'text-primary text-xs')),
            "min": (rule, split) => concatRules.push(generateJSX(`>=${split[1]}`, 'text-primary text-xs')),
            "date_format": (rule: string , split: string[]) => concatRules.push(generateJSX(`Format: ${split.slice(1).join(' ')}`, 'text-xs  text-gray-500')),
            "regex": (rule: string , split: string[]) => concatRules.push(generateJSX(`Regex: ${split.slice(1).join(' ')}`, 'text-primary text-xs')),
            "nullable": () => concatRules.push(generateJSX('or null', 'text-xs text-gray-500')),
            "exists": () => concatRules.push(generateJSX('exists', 'text-xs text-gray-500')),
            "default": (rule: string) => concatRules.push(generateJSX(rule, 'text-xs text-gray-500'))
        };

        rules.map((rule) => {
            const orderedRule = orderingRules(rule, rules_order || []);
            orderedRule.split('|').map((theRule) => {
                const split = theRule.split(':');
                const handler = ruleHandlers[theRule.split(':')[0]] || ruleHandlers["default"];
                handler(theRule, split);
            });
        });

        return (<>{concatRules}</>);
    }
    return (
        <>
            <tr className="-pb-2">
                <th className='param-cell -pb-2'>
                    <div className="grid grid-cols-6 gap-4">
                        <div className="col-span-2">
                            <span className='text-blue-500 pr-1'>¬</span>
                            <code className='pl-1'>
                                {mainRule}
                            </code>
                        </div>
                        <div className="col-span-4">
                            {FormatRules(rules)}
                        </div>
                    </div>
                    {infos?.[mainRule]?.description && (
                        <div className="collapse collapse-arrow -mb-3">
                            <input type="checkbox"/>
                            <div className="collapse-title text-xs text-slate-500">
                                <InformationCircleIcon className='inline-block h-4 w-4'/>
                                <span className="pl-2">Field Info</span>
                            </div>
                            <div className="collapse-content p-0">
                                {infos?.[mainRule]?.description && (
                                    <div className="grid grid-cols-5 pl-10">
                                        <div className="col-span-1">
                                            <code className="text-xs text-gray-500">Description: </code>
                                        </div>
                                        <div className="col-span-4">
                                            <code
                                                className="text-xs text-gray-500">{infos?.[mainRule]?.description.toString()} </code>
                                        </div>
                                    </div>
                                )}
                                {infos?.[mainRule]?.example.toString() && (
                                    <div className="grid grid-cols-5 pl-10">
                                        <div className="col-span-1">
                                            <code className="text-xs text-gray-500">Example: </code>
                                        </div>
                                        <div className="col-span-4">
                                            <code
                                                className="text-xs text-gray-500">{infos?.[mainRule]?.example.toString()} </code>
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>
                    )}
                </th>
            </tr>
        </>
    )
}
