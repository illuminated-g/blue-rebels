<script>
    import { onMount, createEventDispatcher } from 'svelte'

    const dispatch = createEventDispatcher();

    export let hasCancel = true
    export let cancelText = "Cancel"
    export let submitText = "Submit"
    export let defaultInput = ""
    export let id = ""
    export let placeholder = ""
    export let title = "Modal Title"
    export let prompt = "Enter value"

    let value = ""
    let modal
    let input

    $: emptyName = value.length == 0

    onMount(() => {
        value = defaultInput;

        modal.addEventListener('shown.bs.modal', () => {
            input.focus()
        })
    })

    function submit() {
        dispatch('submit', {
            value
        })
    }

</script>

<div class="modal fade" id={id} bind:this={modal}>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <slot name="header"><h1>{title}</h1></slot>
            </div>
            <form on:submit|preventDefault={submit}>
                <div class="modal-body">
                    <slot name="body">
                        {prompt}
                        <input type="text" class="form-control" placeholder={placeholder} bind:value bind:this={input}>
                    </slot>
                </div>
                <div class="modal-footer">
                    {#if hasCancel}
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{cancelText}</button>
                    {/if}
                    <button type="submit" class="btn btn-primary" disabled="{emptyName}">{submitText}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    
</style>