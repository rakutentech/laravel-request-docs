import React from "react"
import Table from "./Table"

export interface APIRefTableProps {
  controller?: string
  method?: string
  middlewares?: string[]
}

export default function APIRefTable({ controller, method, middlewares }: APIRefTableProps) {
  return (
    <div className="text-sm">
      <Table>
        <Table.Body>
          <Table.Row>
            <Table.HeadCell className="w-32 border-t">Controller</Table.HeadCell>
            <Table.Cell className="text-xs font-mono border-t">{controller}</Table.Cell>
          </Table.Row>
          <Table.Row>
            <Table.HeadCell>Method</Table.HeadCell>
            <Table.Cell className="text-xs font-mono">{`@${method}`}</Table.Cell>
          </Table.Row>
          <Table.Row>
            <Table.HeadCell>Middleware</Table.HeadCell>
            <Table.Cell className="text-xs font-mono">{middlewares?.join(", ")}</Table.Cell>
          </Table.Row>
        </Table.Body>
      </Table>
    </div>
  )
}