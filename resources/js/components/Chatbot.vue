<template>
    <div class="container">
        <div class="row">
            <div class="offset-md-3 col-md-6 border mt-5">

                <div class="border mt-3 p-1" style="height: 400px">
                    <template v-for="message in messages">
                        <div>
                            {{ message.text }}
                        </div>
                    </template>
                </div>

                <div class="row">
                    <div class="col-10">
                        <label class="sr-only" for="inlineFormInputName2">Name</label>
                        <input :type="isTypingPassword ? 'password' : 'text'" class="form-control"
                               id="inlineFormInputName2"
                               placeholder="" v-model="text">
                        {{ text }}
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-primary mb-2" @click="send(text)">Enviar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
const default_layout = "default";

export default {
    mounted() {
        console.log('Component mounted.')
        axios.get('/api/test').then(response => console.log(response))
        // axios.get('/api/test-auth').then(response => console.log(response))
        /* axios.post('/api/register', {
            name: "André",
            email: "andrealoisio+2@gmail.com",
            password: "senhateste123",
            password_confirmation: "senhateste123"
        }).then(response => console.log(response)) */
        /* axios.get('/sanctum/csrf-cookie').then(response => {
            // console.log(response)
            axios.post('/api/login', {
                email: 'andrealoisio@gmail.com',
                password: 'senhateste123'
            }).then(response => {
                console.log(response);
                axios.get('/api/test-auth').then(response => console.log(response))
            })
        }); */
    },
    computed: {},
    data() {
        return {
            messages: [
                {
                    from: 'bot',
                    text: 'Hello! Welcome to your bank account'
                },
                {
                    from: 'bot',
                    text: 'Choose one of the options below to start'
                },
                {
                    from: 'bot',
                    text: 'login register'
                },
            ],
            text: "login",
            nextAction: "",
            acceptedEntries: ['register', 'login', 'logout'],
            isTypingPassword: false,
            username: null,
            password: null
        }
    },
    methods: {
        send: function (text) {

            var entry = null
            var action = null
            if (this.nextAction) {
                action = this.nextAction
                entry = text
            } else {
                if (this.invalidEntry(text)) {
                    return
                }
                action = text
            }

            console.log(action)

            switch (action) {
                case 'register':
                    this.botMessage('Type in your username')
                    this.isTypingPassword = true
                    break
                case 'login':
                    this.botMessage('Type in your username')
                    this.nextAction = 'enter-password'
                    break
                case 'enter-password':
                    this.username = entry
                    this.userMessage(this.username)
                    this.botMessage('Please enter your password')
                    this.isTypingPassword = true
                    this.nextAction = 'try-login'
                    break
                case 'try-login':
                    this.isTypingPassword = false
                    this.nextAction = null
                    axios.post('/api/login', {
                        email: this.username,
                        password: entry
                    }).then(response => {
                        console.log(response);
                        axios.get('/api/test-auth').then(_ => console.log(_))
                        this.botMessage('Login success!')
                        this.clearValues()
                    }).catch(error => {
                        console.log(error)
                    })
                    break
                case 'logout':
                    axios.post('/api/logout').then(response => {
                        this.botMessage('Successfully logged out!')
                    });
                    break
                default:
                    this.botMessage('Invalid option')
            }

            if (text === "register") {

            }
            this.text = "";
        },
        botMessage: function (text) {
            this.messages.push({
                from: 'bot', text
            })
        },
        userMessage: function (text) {
            this.messages.push({
                from: 'user', text
            })
        },
        clearValues: function () {
            this.username = null
            this.password = null
            this.nextAction = null
            this.isTypingPassword = false
        },
        invalidEntry(text) {
            if (this.nextAction) {
                return false
                // todo: pode tratar aqui a saida
            }
            if (this.acceptedEntries.indexOf(text) === -1) {
                this.botMessage('Invalid option')
                return true
            }
            return false
        }
    }
};
</script>
