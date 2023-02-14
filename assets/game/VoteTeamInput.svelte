<div class="h-100 text-center">
    {#if voted}
        <p>You've placed your vote</p>
    {:else}
        <button class="btn btn-success" on:click={() => {vote(true)}}>Approve</button>
        <button class="btn btn-danger" on:click={() => {vote(false)}}>Reject</button>
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