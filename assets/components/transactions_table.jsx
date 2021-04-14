import React from "react"
import DayJS from "react-dayjs/source";

export const TransactionsTable = ({transactions}) => {
    return (
        <table className="max-w-max	 whitespace-no-wrap">
            <thead>
            <tr
                className="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800"
            >
                <th className="px-4 py-3">Date</th>
                <th className="px-4 py-3">Description</th>
                <th className="px-4 py-3">Montant</th>
            </tr>
            </thead>
            <tbody
                className="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800"
            >
            {transactions.map((t) => {
                return (
                    <tr key={t['@id']} className="text-gray-700 dark:text-gray-400">
                        <td className="px-4 py-3">
                            <div className="flex items-center text-sm">
                                <div>
                                    <p className="font-semibold"><DayJS format={"DD/MM/YYYY HH:mm"}>{t.createdAt}</DayJS></p>
                                </div>
                            </div>
                        </td>
                        <td className="px-4 py-3 text-sm">
                            {t.description === null ? `Virement ${(t.type === 'credit') ? 'reçu' : 'émis'}` : t.description}
                        </td>
                        <td className="px-4 py-3 text-xs">
                        <span
                            className={`px-2 py-1 font-semibold leading-tight ${t.type === 'credit' ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100'} rounded-full dark:bg-green-700 dark:text-green-100`}
                        >
                            {t.type === 'credit' ? '+' : '-'} {t.amount} €
                        </span>
                        </td>
                    </tr>)
            })}

            </tbody>
        </table>
    )
}