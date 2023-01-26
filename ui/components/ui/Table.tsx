import React from "react"

export default function Table({
  children,
  className,
  ...props
}: React.TableHTMLAttributes<HTMLTableElement>): JSX.Element {
  return (
    <table className={"text-left w-full border-collapse " + (className ?? "")} {...props}>
      {children}
    </table>
  )
}

Table.Head = function TableHead({
  children,
  ...props
}: React.ThHTMLAttributes<HTMLTableSectionElement>): JSX.Element {
  return <thead {...props}>{children}</thead>
}

Table.HeadCell = function TableHeadCell({
  children,
  className,
  ...props
}: React.HTMLAttributes<HTMLTableCellElement>): JSX.Element {
  return (
    <th
      className={
        "border-b border-base-content/20 font-semibold leading-6 py-2 "
        + (className ?? "")
      }
      {...props}
    >
      {children}
    </th>
  )
}

Table.Body = function TableBody({
  children,
  className,
  ...props
}: React.HTMLAttributes<HTMLTableSectionElement>): JSX.Element {
  return (
    <tbody className={(className ?? "")} {...props}>
      {children}
    </tbody>
  )
}

Table.Row = function TableRow({
  children,
  className,
  ...props
}: React.HTMLAttributes<HTMLTableRowElement>): JSX.Element {
  return (
    <tr
      className={(className ?? "")}
      {...props}
    >
      {children}
    </tr>
  )
}

Table.Cell = function TableCell({
  children,
  className,
  ...props
}: React.TdHTMLAttributes<HTMLTableCellElement>): JSX.Element {
  return (
    <td
      className={"border-b py-2 border-base-content/20 " + (className ?? "")}
      {...props}
    >
      {children}
    </td>
  )
}
