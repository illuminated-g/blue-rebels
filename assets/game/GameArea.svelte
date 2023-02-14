<div class="h-100">
    <div class="area-header">
        {gameName}<br/>
        {#if leader} Current Mission Leader: {leader}{/if}<br/>
        You are: <span class="player-name">{playerName}</span> {#if whichside != ""}and are <span class="side-{whichside}">{whichside}</span>{/if}<br/>
        Successes: <span class="side-GOOD">{successes}</span> and Failures: <span class="side-BAD">{failures}</span><br/>
        Rejected mission count: {rejectedCount} (Good group loses at 5)
    </div>
    <div class="main-area row my-3 h-100">
        <div class="col-2">
            <PlayerList players={teamPlayers} player={player} label="Current Team"/>
        </div>
        <div class="col-8 play-area text-center h-100">
            <div class="row my-5">
                <div class="col">
                    {message}
                </div>
            </div>
            <div class="row input-area">
                <div class="col">
                    {#if inputType == "NominateTeam"}
                        <TeamSelectInput bind:game bind:area/>
                    {:else if inputType == "VoteTeam"}
                        <VoteTeamInput bind:game bind:area/>
                    {:else if inputType == "VoteMission"}
                        <VoteMissionInput bind:game bind:area/>
                    {:else if inputType == "OkayButton"}
                        <OkayButtonInput bind:game bind:area/>
                    {/if}
                </div>
            </div>
            <div class="row mt-3 hint-row py-5">
                <div class="col">
                    {hint}
                </div>
            </div>
        </div>
        <div class="col-2">
            <PlayerList bind:players={previousTeam} bind:player label="Previous Team"/>
        </div>
    </div>
</div>

<script>

    import PlayerList from './PlayerList.svelte'
    import TeamSelectInput from './TeamSelectInput.svelte'
    import VoteTeamInput from './VoteTeamInput.svelte'
    import VoteMissionInput from './VoteMissionInput.svelte'
    import OkayButtonInput from './OkayButtonInput.svelte'

    export let game
    export let state
    export let player
    export let area
    export let leader = ""
    export let isbad

    let teamPlayers = []
    let previousTeam = []
    let hasVoted = false
    let rejectedCount = 0
    let successes = 0;
    let failures = 0;

    $: gameName = game ? game.name : ""
    $: statePhase = state ? state.phase : ""
    $: playerName = player ? player.name : ""
    $: message = area ? area.message : ""
    //$: inputType = area ? area.inputType : "None"
    $: hint = area && area.hint ? area.hint : ""
    $: whichside = (isbad === undefined) ? "" : (isbad ? "BAD" : "GOOD")

    $: hasVoted = area ? area.hasVoted : false
    $: inputType = area ? area.inputType : "None"

    $: {
        if (game && state) {
            rejectedCount = state.rejectedCount
            successes = state.successes;
            failures = state.failures;

            let team = [];
            for (let i = 0; i < state.teamPlayerIndices.length; ++i) {
                team.push(game.players[state.teamPlayerIndices[i]])
            }
            teamPlayers = team;

            team = [];
            for (let i = 0; i < state.previousTeam.length; ++i) {
                team.push(game.players[state.previousTeam[i]])
            }
            previousTeam = team;
        } else {
            teamPlayers = []
        }
    }

</script>

<style>
    .area-header {
        background-color: #222;
        padding: 0.5rem;
    }

    .player-name {
        color: cornflowerblue;
    }

    .main-area {
        height: 100%;
        background-color: #222;
    }

    .input-area {
        background-color: #444;
    }

    .play-area {
        position: relative;
    }

    .hint-row {
        position: absolute;
        bottom: 0;
    }

    .side-GOOD {
        color:green;
    }

    .side-BAD {
        color: #D42;
    }
</style>