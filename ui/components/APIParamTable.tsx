import React from "react"
import Table from "./Table"

export interface APIParamTableProps {
  params: APIRule;
}

const RuleNames = {
  required: ["required"],
  types: ["string","integer","numeric","array", "boolean"],
}

interface IRules {
  required?: string[],
  type?: string[],
  rules?: string[],
}

function getRules(ruleStr: string): IRules {
  const rules = ruleStr.split("|")

  return {
    required: rules.filter((val) => RuleNames.required.includes(val)) ?? ["optional"],
    type: rules.filter((val) => RuleNames.types.includes(val)),
    rules: rules.filter((val) => ![...RuleNames.required, ...RuleNames.types].includes(val))
  }
}

export default function APIParamTable({ params }: APIParamTableProps) {
  const rules = Object.keys(params)
  return (
    <>
      <div className="py-3"></div>
      <h4 className="">Parameters</h4>
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
                  <Table.Cell className="whitespace-pre-wrap text-xs font-mono">{ruleDetails.required?.join("\n")}</Table.Cell>
                  <Table.Cell className="whitespace-pre-wrap text-xs font-mono">{ruleDetails.type?.join("\n")}</Table.Cell>
                  <Table.Cell className="whitespace-pre-wrap text-xs font-mono">{ruleDetails.rules?.join("\n")}</Table.Cell>
                </Table.Row>
              )
            })}
          </Table.Body>
        </Table>
      </div></>
  )
}