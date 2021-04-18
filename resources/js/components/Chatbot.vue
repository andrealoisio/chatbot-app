<template>
    <div class="container">
        <div class="row">
            <div class="offset-md-3 col-md-6 border mt-5">

                <div id="chat-messages" class="border mt-3 p-1" style="height: 400px; overflow-x: auto">
                    <template v-for="message in messages">
                        <!--                        <div v-bind:class="{ 'text-right' : message.from === 'user' }">-->
                        <!--                            {{ message.text }}-->
                        <!--                        </div>-->
                        <div v-bind:class="{ 'text-right' : message.from === 'user' }">
                            <div
                                :class="{
                                'alert p-0 d-inline' : true,
                                'alert-success': message.type === 'success',
                                'alert-danger' : message.type === 'error',
                                'alert-light' : message.type === 'light'}"
                                role="alert">
                                {{ message.text }}
                            </div>
                        </div>
                    </template>
                </div>

                <div class="row">
                    <div class="col-10">
                        <label class="sr-only" for="inlineFormInputName2">Name</label>
                        <input :type="isTypingPassword ? 'password' : 'text'" class="form-control"
                               id="inlineFormInputName2"
                               placeholder="" v-model="text" v-on:keyup.enter="send(text)">
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
const SUCCESS = 'success';
const ERROR = 'error';
const PASSWORD_PLACEHOLER = "****************";

export default {
    mounted() {
        console.log('Component mounted.')
        // axios.get('/api/test').then(response => console.log(response))
        // axios.get('/api/test-auth').then(response => console.log(response))

        // axios.post('/api/register', {
        //     name: "André",
        //     email: "andrealoisio@gmail.com",
        //     password: "senhateste123",
        //     password_confirmation: "senhateste123"
        // }).then(response => console.log(response))

        // axios.get('/sanctum/csrf-cookie').then(response => {
        //     // console.log(response)
        //     axios.post('/api/login', {
        //         email: 'andrealoisio@gmail.com',
        //         password: 'senhateste123'
        //     }).then(response => {
        //         console.log(response);
        //         axios.get('/api/test-auth').then(response => console.log(response))
        //     })
        // });
    },
    computed: {},
    data() {
        return {
            messages: [
                {
                    from: 'bot',
                    text: 'Hello! Welcome to your bank account',
                },
                {
                    from: 'bot',
                    text: 'Choose one of the options below to start',
                },
                {
                    from: 'bot',
                    text: 'login register'
                },
            ],
            text: "",
            nextAction: "",
            acceptedEntries: ['register', 'login', 'logout'],
            isTypingPassword: false,
            username: null,
            email: null,
            password: null,
            password_confirmation: null
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
                    this.botMessage('Enter your name')
                    this.nextAction = 'register-name'
                    break
                case 'register-name':
                    this.userMessage(entry)
                    this.username = entry
                    this.botMessage('Enter your e-mail')
                    this.nextAction = 'register-email'
                    break
                case 'register-email':
                    this.userMessage(entry)
                    this.email = entry
                    this.botMessage('Enter your password')
                    this.isTypingPassword = true
                    this.nextAction = 'register-password'
                    break
                case 'register-password':
                    this.userMessage(PASSWORD_PLACEHOLER)
                    this.password = entry
                    this.botMessage('Enter your password confirmation')
                    this.isTypingPassword = true
                    this.nextAction = 'register-password-confirmation'
                    break
                case 'register-password-confirmation':
                    this.userMessage(PASSWORD_PLACEHOLER)
                    this.password_confirmation = entry
                    this.botMessage('Trying to register')
                    const registrationBody = {
                        name: this.username,
                        email: this.email,
                        password: this.password,
                        password_confirmation: this.password_confirmation
                    }
                    console.log(registrationBody)
                    axios.post('/api/register', registrationBody).then(response => {
                        console.log(response)
                    }).catch(error => {
                        if (error.response) {
                            console.log(error.response)
                            const {message} = error.response.data
                            this.botMessage(message, ERROR)
                        }
                    })
                    this.nextAction = null
                    this.clearValues()
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
                    this.userMessage(PASSWORD_PLACEHOLER)
                    axios.post('/api/login', {
                        email: this.username,
                        password: entry
                    }).then(response => {
                        console.log(response);
                        axios.get('/api/test-auth').then(_ => console.log(_))
                        this.botMessage('Login success!', SUCCESS)
                        this.clearValues()
                    }).catch(error => {
                        if (error.response) {
                            const {type, message} = error.response.data
                            this.botMessage(message, type)
                        } else {
                            this.botMessage('Somethign went wrong!', ERROR)
                        }
                    })
                    break
                case 'logout':
                    axios.post('/api/logout').then(response => {
                        this.botMessage('Successfully logged out!', SUCCESS)
                    });
                    break
                default:
                    this.botMessage('Invalid option', ERROR)
            }

            if (text === "register") {

            }
            this.text = "";
        },
        botMessage: function (text, type = '') {
            this.messages.push({
                from: 'bot', text, type
            })
            this.scroolChat()
        },
        userMessage: function (text) {
            this.messages.push({
                from: 'user', text, type: 'light'
            })
            this.scroolChat()
        },
        scroolChat: function() {
            setTimeout(() => {
                let objet =this.$el.querySelector('#chat-messages')
                objet.scrollTop = objet.scrollHeight;
            },0)
        },
        clearValues: function () {
            this.username = null
            this.email = null
            this.password = null
            this.password_confirmation = null
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
