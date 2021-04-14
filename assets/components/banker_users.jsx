import React from "react"
import {render} from "react-dom"

export const BankerUsers = () => {
    return (
        <h1>Hello word</h1>
    )
}

// custom element for linking react and dom
class BankerUsersComponent extends HTMLElement{
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