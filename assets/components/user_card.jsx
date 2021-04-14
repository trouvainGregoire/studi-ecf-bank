import React from "react"
import DayJS from "react-dayjs/source";
import {TransactionsTable} from "./transactions_table";

export const UserCard = ({user}) => {
    return (
        <div className="flex items-center justify-center px-4 overflow-auto p-4">

            <div className="max-w-4xl bg-white w-full rounded-lg shadow-xl mb-7 md:mb-0">
                <div className="p-4 border-b">
                    <h2 className="text-2xl ">
                        Informations du client
                    </h2>
                </div>
                <div>
                    <div className="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-4 border-b">
                        <p className="text-gray-600">
                            Nom complet
                        </p>
                        <p>
                            {user.name} {user.firstname}
                        </p>
                    </div>
                    <div className="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-4 border-b">
                        <p className="text-gray-600">
                            Date de naissance
                        </p>
                        <p>
                            <DayJS format={"DD/MM/YYYY"}>{user.birthdate}</DayJS>
                        </p>
                    </div>
                    <div className="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-4 border-b">
                        <p className="text-gray-600">
                            Email
                        </p>
                        <p>
                            {user.email}
                        </p>
                    </div>
                    <div className="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-4 border-b">
                        <p className="text-gray-600">
                            Adresse
                        </p>
                        <p>
                            {user.address}, {user.zipcode}, {user.city}
                        </p>
                    </div>
                    <div className="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-4 border-b">
                        <p className="text-gray-600">
                            Numéro de compte
                        </p>
                        <p>
                            {user.account.identifier}
                        </p>
                    </div>
                    <div className="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-4 border-b">
                        <p className="text-gray-600">
                            Solde du compte
                        </p>
                        <p>
                            {user.account.balance} €
                        </p>
                    </div>
                    <div className="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-4">
                        <p className="text-gray-600">
                            Pièce d'identité
                        </p>
                        <div className="space-y-2">
                            <div className="border-2 flex items-center p-2 rounded justify-between space-x-2">
                                <div className="space-x-2 truncate">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         className="fill-current inline text-gray-500" width="24" height="24"
                                         viewBox="0 0 24 24">
                                        <path
                                            d="M17 5v12c0 2.757-2.243 5-5 5s-5-2.243-5-5v-12c0-1.654 1.346-3 3-3s3 1.346 3 3v9c0 .551-.449 1-1 1s-1-.449-1-1v-8h-2v8c0 1.657 1.343 3 3 3s3-1.343 3-3v-9c0-2.761-2.239-5-5-5s-5 2.239-5 5v12c0 3.866 3.134 7 7 7s7-3.134 7-7v-12h-2z"/>
                                    </svg>
                                    <span>
                                        {user.idCardName}
                            </span>
                                </div>
                                <a target="_blank"
                                   href={`https://ecf-bank.s3.eu-west-3.amazonaws.com/${user.idCardName}`}
                                   className="text-purple-700 hover:underline">
                                    Voir
                                </a>
                            </div>
                        </div>
                    </div>
                    <div className="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-4 border-b">
                        <p className="text-gray-600">
                            Transactions
                        </p>
                        <TransactionsTable transactions={user.account.transactions}/>

                    </div>
                </div>
            </div>
        </div>
    )
}