<div class="container-md text-center mb-5">
    <p>Update your name:</p>
    <form on:submit|preventDefault={updateName}>
        <input type="text" class="form-control" placeholder="Name" bind:this={input}/>
        <span class="small">Press enter to confirm new name.</span>
    </form>
</div>

<script>
    import axios from "axios";
    import { createEventDispatcher } from "svelte"

    let dispatch = createEventDispatcher();

    export let player
    let input
    let name = ''

    $: if (player) {
        updatePlayer()
    }

    function updatePlayer() {
        if (player.name != '' && player.name != name) {
            input.value = player.name
            name = player.name
        }
    }

    function updateName() {
        axios.post(
            '/player/' + player.id + '/name',
            {
                name: input.value
            }
        ).then((response) => {
            dispatch('update', {name: response.data.name})
        })
    }
</script>