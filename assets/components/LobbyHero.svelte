<div class="px-4 py-5 my-5 text-center">
    <h1>{name}</h1>
    <div class="mx-auto">
        <p class="lead mb-4">Waiting for {canStart ? "you" : "the game creator"} to start once everyone has joined.</p>
        {#if code == ""}
            <p>Invalid game code, try refreshing.</p>
        {:else}
            <p>Join url: <strong>http://{window.location.host + '/g/' + code}</strong></p>
        {/if}

        {#if canStart}
            <div class="container mx-auto">
                <button class="btn btn-primary" on:click={startGame} disabled={!hasMinPlayers}>Start Game</button>
                <p class="small"><em>{startTooltip}</em></p>
            </div>
        {/if}
    </div>
</div>

<script>
    import { createEventDispatcher } from "svelte"

    let dispatch = createEventDispatcher()

    export let name = "Blue Rebels"
    export let code = ""
    export let canStart = false
    export let hasMinPlayers

    $: startTooltip = hasMinPlayers ? "" : "Not enough players yet."

    function startGame() {
        dispatch('start')
    }

</script>

<style>

</style>