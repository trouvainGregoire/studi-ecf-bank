import React, {useEffect, useState} from "react"
import {render} from "react-dom"
import Loader from "react-loader-spinner"
import {UserTable} from "./user_table";
import {BankerTable} from "./bank_table";

export const BankerUsers = () => {
    const [activatedUsers, setActivatedUsers] = useState([])
    const [isLoading, setIsLoading] = useState(true)

    useEffect(() => {
        async function getActivatedUsers() {
            const response = await fetch('/api/clients', {method: 'GET', headers: {'Accept': 'application/ld+json'}})
            if (response.ok) {
                const responseJson = await response.json();
                const users = responseJson['hydra:member']

                users.map((user) => {
                    user.account.transactions.sort((a, b) => {
                        return new Date(b.createdAt) - new Date(a.createdAt)
                    })
                })

                setActivatedUsers(users)
                setIsLoading(false)
            } else {
                setIsLoading(false)
            }
        }

        getActivatedUsers()
    }, [])

    return (
        <>
            {isLoading && <Loader type="BallTriangle" color="#00BFFF" height={80} width={80}/>}

            {activatedUsers.length > 0 ?
                <BankerTable users={activatedUsers} />
            : ''
            }

        </>
    )
}

// custom element for linking react and dom
class BankerUsersComponent extends HTMLElement {
    connectedCallback() {
        render(<BankerUsers/>, this)
    }

    disconnectedCallback() {
        unmountComponentAtNode(this)
    }
}

if (!customElements.get('banker-users')) {
    customElements.define('banker-users', BankerUsersComponent)
}