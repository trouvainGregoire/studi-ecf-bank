import React from "react"

export const Modal = ({handleClose, children}) => {
    return (
        <div className="modal opacity-100 pointer-events-auto fixed w-full h-full top-0 left-0 flex items-center justify-center">
            <div
                className="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"
                onClick={handleClose}
                role="button"
                tabIndex={-1}
                aria-label="Fermer"
            />
            <div
                className="modal-container bg-white mx-auto rounded shadow-lg z-10 max-h-screen overflow-auto"
            >
                <div className="modal-content overflow-auto max-h-screen pt-20 md:pt-0">
                    {children}
                    <div className="flex items-center py-2 justify-center md:hidden">
                        <button onClick={handleClose} className="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-blue-600 border border-transparent rounded-lg active:bg-blue-600 hover:bg-blue-700 focus:outline-none focus:shadow-outline-blue">
                            Fermer
                        </button>
                    </div>

                </div>
            </div>
        </div>
    )
}