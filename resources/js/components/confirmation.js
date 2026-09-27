const confirmationModal = document.getElementById(
    'confirmationModal'
);

const confirmationForm = document.getElementById(
    'confirmationModalForm'
);

const confirmationMethod = document.getElementById(
    'confirmationModalMethod'
);

const confirmationTitle = document.getElementById(
    'confirmationModalTitle'
);

const confirmationMessage = document.getElementById(
    'confirmationModalMessage'
);

const confirmationSubmit = document.getElementById(
    'confirmationModalSubmit'
);

const confirmationIcon = document.getElementById(
    'confirmationModalIcon'
);


function openConfirmation({
    title = 'Konfirmasi',
    message = 'Apakah Anda yakin?',
    action,
    method = 'POST',
    type = 'warning',
    submitText = 'Ya, Lanjutkan',
    icon = 'bi-question-lg',
}) {

    if (!confirmationModal) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Content
    |--------------------------------------------------------------------------
    */

    confirmationTitle.textContent = title;

    confirmationMessage.textContent = message;

    confirmationSubmit.textContent = submitText;


    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    confirmationForm.action = action;

    confirmationMethod.value = method;


    /*
    |--------------------------------------------------------------------------
    | Type
    |--------------------------------------------------------------------------
    */

    confirmationModal.classList.remove(
        'is-danger',
        'is-warning'
    );

    if (type === 'danger') {

        confirmationModal.classList.add(
            'is-danger'
        );

    } else {

        confirmationModal.classList.add(
            'is-warning'
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Icon
    |--------------------------------------------------------------------------
    */

    confirmationIcon.innerHTML = `
        <i class="bi ${icon}"></i>
    `;


    /*
    |--------------------------------------------------------------------------
    | Open
    |--------------------------------------------------------------------------
    */

    confirmationModal.classList.add(
        'is-open'
    );

    confirmationModal.setAttribute(
        'aria-hidden',
        'false'
    );

    document.body.classList.add(
        'modal-open'
    );

}


function closeConfirmation() {

    if (!confirmationModal) {
        return;
    }

    confirmationModal.classList.remove(
        'is-open'
    );

    confirmationModal.setAttribute(
        'aria-hidden',
        'true'
    );

    document.body.classList.remove(
        'modal-open'
    );

}


/*
|--------------------------------------------------------------------------
| Close Button / Backdrop
|--------------------------------------------------------------------------
*/

document.addEventListener('click', function (event) {

    if (
        event.target.matches(
            '[data-confirmation-close]'
        )
    ) {

        closeConfirmation();

    }

});


/*
|--------------------------------------------------------------------------
| ESC
|--------------------------------------------------------------------------
*/

document.addEventListener('keydown', function (event) {

    if (
        event.key === 'Escape' &&
        confirmationModal?.classList.contains('is-open')
    ) {

        closeConfirmation();

    }

});


window.openConfirmation = openConfirmation;

window.closeConfirmation = closeConfirmation;