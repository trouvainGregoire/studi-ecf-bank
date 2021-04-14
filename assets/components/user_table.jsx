import React from "react"

export const UserTable = ({user, handleClick}) => {
    return (
        <>
            <tr className="text-gray-700 dark:text-gray-400 cursor-pointer" onClick={() => handleClick(user)}>
                <td className="py-3">
                    <div className="flex items-center text-sm">
                        <div
                            className="relative hidden w-8 h-8 rounded-full md:block"
                        >
                        </div>
                        <div>
                            <p className="font-semibold">{user.name} {user.firstname}</p>
                            <p className="text-xs text-gray-600 dark:text-gray-400">
                                {user.email}
                            </p>
                        </div>
                    </div>
                </td>
                <td className="px-4 py-3 text-sm">
                    {user.account.identifier}
                </td>
                <td className="px-4 py-3 text-sm">
                    {user.account.balance} €
                </td>
            </tr>
        </>
    )
}