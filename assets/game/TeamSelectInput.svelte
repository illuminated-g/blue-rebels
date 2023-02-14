<div class="team-input h-100">
    <button class="btn btn-primary mb-2" disabled={!canNominate} on:click={nominate}>Nominate</button>
    <ul class="list-group small text-start">
        {#each players as p, i (p.id)}
            <li class="list-group-item">
                <input class="form-check-input me-1" id={"team_select_" + i} type="checkbox" bind:group={selected} value={i}>
                <label class="form-check-label" for={"team_select_" + i}>{p.name}</label>
            </li>
        {/each}
    </ul>
</div>

<script>
    import axios from 'axios'
    export let game
    export let area

    let selected = []

    $: canNominate = area ? selected.length == area.teamCount : false

    $: players = game ? game.players : []

    function nominate() {
        if (canNominate) {
            axios.post(
                '/game/' + game.code + '/nominate',
                {team: selected}
            )
        } else {
            alert('Incorrect team size selected.')
        }
    }
</script>