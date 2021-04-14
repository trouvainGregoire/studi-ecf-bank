import React, {useState} from "react"
import {UserTable} from "./user_table";
import {Modal} from "./modal";
import {UserCard} from "./user_card";

export const BankerTable = ({users}) => {

    const [isModal, setIsModal] = useState(false)
    const [currentUser, setCurrentUser] = useState(null)

    const handleClick = (user) => {
        setCurrentUser(user)
        setIsModal(true)
    }

    const closeModal = () => {
        setCurrentUser(null)
        setIsModal(false)
    }


    return (
        <>
            <table className="w-full whitespace-no-wrap mt-2 mb-20">
                <thead>
                <tr
                    className="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800"
                >
                    <th className="px-4 py-3">Client</th>
                    <th className="px-4 py-3">Numéro de compte bancaire</th>
                    <th className="px-4 py-3">Solde du compte bancaire</th>
                </tr>
                </thead>
                <tbody
                    className="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800"
                >
                {users.map((u) => (<UserTable key={u.id} handleClick={handleClick} user={u}/>))}
                </tbody>
            </table>
            {isModal && <Modal handleClose={closeModal}><UserCard user={currentUser} /></Modal>}
        </>
    )
}