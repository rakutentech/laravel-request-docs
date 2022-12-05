import React from "react"
import { AdjustmentsHorizontalIcon } from "@heroicons/react/24/outline"

import Table from "./Table"

export interface APIParamTableProps {
  params: IAPIRule;
}

const RuleNames = {
  required: ["required"],
  types: ["string", "integer", "numeric", "array", "boolean"],
}

interface IRules {
  required?: boolean,
  type?: string[],
  rules?: string[],
}

function getRules(ruleStr: string): IRules {
  const rules = ruleStr.split("|")

  return {
    required: rules.findIndex((val) => RuleNames.required.includes(val)) >= 0,
    type: rules.filter((val) => RuleNames.types.includes(val)),
    rules: rules.filter((val) => ![...RuleNames.required, ...RuleNames.types].includes(val))
  }
}

export default function APIParamTable({ params }: APIParamTableProps) {
  const rules = Object.keys(params)
  return (
    <div className="py-3">
      <div className="w-full border-b border-b-base-content/20">
        <h4 className="flex items-center space-x-2 text-base">
          <AdjustmentsHorizontalIcon className="h-4 w-4" />
          <span>Parameters</span>
        </h4>
      </div>
      <div className="text-sm">
        <Table>
          <Table.Head>
            <Table.Row>
              <Table.HeadCell>Attributes</Table.HeadCell>
              <Table.HeadCell>Required</Table.HeadCell>
              <Table.HeadCell>Type</Table.HeadCell>
              <Table.HeadCell>Rules</Table.HeadCell>
            </Table.Row>
          </Table.Head>
          <Table.Body>
            {rules.map((r, i) => {
              const ruleDetails = getRules(params[r].join("|"))
              return (
                <Table.Row key={`${r}_${i}`}>
                  <Table.Cell>{r}</Table.Cell>
                  <Table.Cell className="whitespace-pre-wrap font-mono">
                    {!!ruleDetails.required ? (
                      <span className="font-bold text-base-content/80">required</span>
                    ) : "optional"
                    }
                  </Table.Cell>
                  <Table.Cell className="whitespace-pre-wrap font-mono">{ruleDetails.type?.join("\n")}</Table.Cell>
                  <Table.Cell className="whitespace-pre-wrap font-mono">{ruleDetails.rules?.join("\n")}</Table.Cell>
                </Table.Row>
              )
            })}
          </Table.Body>
        </Table>
      </div>
    </div>
  )
}