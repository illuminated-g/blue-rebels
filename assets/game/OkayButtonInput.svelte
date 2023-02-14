<div class="h-100 text-center">
    {#if voted}
        <p>Waiting for others.</p>
    {:else}
        <button class="btn btn-info" on:click={() => {vote(true)}}>Okay</button>
    {/if}
</div>

<script>
    import axios from "axios";

    export let game
    export let area

    $: voted = area ? area.hasVoted : false

    function vote(approved) {
        axios.post(
            '/game/' + game.code + '/vote',
            {
                vote: approved
            }
        ).then(() => {
            voted = true;
        })
    }

</script>