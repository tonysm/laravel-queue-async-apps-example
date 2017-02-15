<template>
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading clearfix">
                        <div class="panel-title pull-left">
                            Servers ({{ servers.length }})
                        </div>

                        <div class="btn-group pull-right">
                            <button
                                    class="btn btn-primary btn-sm"
                                    title="Create a new server"
                                    data-toggle="modal" data-target="#newServerModal"
                            >
                                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                            </button>

                            <div class="modal fade" id="newServerModal" tabindex="-1" role="dialog" aria-labelledby="newServerModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="newServerModalLabel">New Server Confirmation</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div>
                                                <div class="form-group">
                                                    <label for="serverName">Server name</label>
                                                    <input disabled type="text" class="form-control" id="serverName" :value="newServerName">
                                                </div>
                                                <button
                                                        type="button"
                                                        class="btn btn-default"
                                                        title="Generate new name"
                                                        @click="changeNewServerName"
                                                >
                                                    <span class="glyphicon glyphicon-refresh"></span>
                                                    Generate new name
                                                </button>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" :disabled="creating" data-dismiss="modal">No</button>
                                            <button type="button" class="btn btn-primary" :disabled="creating" @click="createNewServer">Yes, create it.</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Server</th>
                                    <th>Status</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-if="servers.length === 0">
                                    <td colspan="3">
                                        You haven't created any server yet.
                                    </td>
                                </tr>
                                <tr v-for="server in servers">
                                    <td>
                                        {{ server.name }}
                                    </td>

                                    <td>
                                        <span class="glyphicon glyphicon-refresh glyphicon-spin" v-if="server.status === 'creating' || server.status === 'provisioning'"></span>
                                        <span class="glyphicon glyphicon-check" v-if="server.status === 'ready'"></span>
                                        {{ server.status }}
                                    </td>

                                    <td>
                                        <button
                                                class="btn btn-sm"
                                                type="button"
                                                data-toggle="modal"
                                                :data-target="'#serverLogs' + server.id"
                                                :disabled="server.status !== 'ready'"
                                        >
                                            <span class="glyphicon glyphicon-search"></span>
                                            View Logs
                                        </button>

                                        <button
                                                class="btn btn-sm btn-danger"
                                                type="button"
                                                :disabled="server.status !== 'ready' && server.status !== 'failed'"
                                                data-toggle="modal"
                                                :data-target="'#serversRemove' + server.id"
                                        >
                                            <span class="glyphicon glyphicon-remove"></span>
                                            Remove
                                        </button>

                                        <div class="modal fade" :id="'serverLogs' + server.id" tabindex="-1" role="dialog" :aria-labelledby="'serverLogsModalLabel' + server.id">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title" :id="'serverLogsModalLabel' + server.id">Server {{ server.name }} Logs</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <pre v-text="server.logs"></pre>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade" :id="'serversRemove' + server.id" tabindex="-1" role="dialog" :aria-labelledby="'serversRemoveLabel' + server.id">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title" :id="'serversRemoveLabel' + server.id">Remove Confirmation</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure you want to remove server <strong>{{ server.name }}</strong>?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn" data-dismiss="modal">Cancel</button>
                                                        <button type="button" class="btn btn-primary" data-dismiss="modal" @click="removeServer(server)">OK</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Haikunator from 'haikunator';

    const generator = new Haikunator({});

    export default {
        name: 'ServersComponent',
        data() {
            return {
                servers: [],
                newServerName: null,
                creating: false,
            };
        },
        created() {
            this.changeNewServerName();

            this.loadServers();
            this.loadCurrentUserAndListen();
        },
        methods: {
            loadServers() {
                axios.get('/api/servers')
                    .then(({data}) => {
                        this.servers = data;
                    });
            },
            createNewServer () {
                this.creating = true;
                axios
                    .post(
                        '/api/servers',
                        { name: this.newServerName },
                        { timeout: 2000 }
                    )
                    .then(({data}) => {
                        this.creating = false;
                        this.servers.push(data);
                        this.changeNewServerName();
                        $('#newServerModal').modal('hide');
                    })
            },
            changeNewServerName () {
                this.newServerName = generator.haikunate({tokenChars: "HAIKUNATE"});
            },
            loadCurrentUserAndListen () {
                axios.get('/api/user')
                    .then(({data}) => {
                        this.listen(data.id);
                    });
            },
            listen (userId) {
                Echo.private('servers.' + userId)
                    .listen('ServerWasCreatedOnProvider', (event) => {
                        this.servers = _.map(this.servers, (server) => {
                            if (server.id === event.server.id) {
                                server = Object.assign({}, server, event.server);
                            }

                            return server;
                        });
                    })
                    .listen('ServerWasProvisioned', (event) => {
                        this.servers = _.map(this.servers, (server) => {
                            if (server.id === event.server.id) {
                                server = Object.assign({}, server, event.server);
                            }

                            return server;
                        });
                    });
            },
            removeServer(server) {
                axios.delete(`/api/servers/${server.id}`).
                    then(() => {
                        this.servers = _.filter(this.servers, (s) => s.id !== server.id);
                    });
            }
        }
    }
</script>
