import React from "react"
import { LinkIcon } from "@heroicons/react/24/outline"

import Table from "./ui/Table"

export interface APIRefTableProps {
  controller?: string
  method?: string
  middlewares?: string[]
}

export default function APIRefTable({ controller, method, middlewares }: APIRefTableProps) {
  return (
    <div className="py-3">
      <div className="w-full border-b border-b-base-content/20">
        <h4 className="text-base flex items-center space-x-2">
          <LinkIcon className="h-4 w-4" />
          <span>API Reference</span>
        </h4>
      </div>
      <div className="text-sm">
        <Table>
          <Table.Body>
            <Table.Row>
              <Table.HeadCell className="w-32">Controller</Table.HeadCell>
              <Table.Cell className="font-mono">{controller}</Table.Cell>
            </Table.Row>
            <Table.Row>
              <Table.HeadCell>Method</Table.HeadCell>
              <Table.Cell className="font-mono">{`@${method}`}</Table.Cell>
            </Table.Row>
            <Table.Row>
              <Table.HeadCell>Middleware</Table.HeadCell>
              <Table.Cell className="whitespace-pre-wrap font-mono">{middlewares?.join("\n")}</Table.Cell>
            </Table.Row>
          </Table.Body>
        </Table>
      </div>
    </div>

  )
}