<template>
    <div class="container">
        <div class="row">
            <div class="offset-md-3 col-md-6 border mt-5">
                <h3 class="text-center mt-3">PHP Challenge<br>Chatbot App</h3>
                <div id="chat-messages" class="border mt-3 p-1" style="height: 400px; overflow-x: auto">
                    <template v-for="message in messages">
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
                    <div v-if="loading" class="spinner-border spinner-border-sm" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>

<!--                <div class="row">-->
<!--                    <div class="col-10">-->
<!--                    </div>-->
<!--                    <div class="col-2">-->
<!--                        <button type="button" class="btn btn-primary mb-2" @click="send(text)">Send</button>-->
<!--                    </div>-->
<!--                </div>-->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="input-group mb-3">
                            <label class="sr-only" for="inlineFormInputName2">Name</label>
                            <input :type="isTypingPassword ? 'password' : 'text'" class="form-control"
                                   id="inlineFormInputName2"
                                   placeholder="" v-model="text" v-on:keyup.enter="send(text)">
                            <button @click="send(text)" class="btn btn-primary" type="button" id="button-addon2">Send</button>
                        </div>
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
const translateToAction = require('./translateToAction').translateToAction
const util = require('./util')

export default {
    mounted() {

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
            loading: false,
            acceptedEntries: ['register', 'login', 'logout'],
            isTypingPassword: false,
            username: null,
            defaultCurrency: null,
            email: null,
            password: null,
            password_confirmation: null,
            loggedIn: true,
            ammount: null,
            currencyCode: null
        }
    },
    methods: {
        send: function (text) {

            var entry = null
            var action = null
            var actions = null
            entry = text
            this.text = null
            this.userMessage(entry)
            if (this.nextAction) {
                action = this.nextAction
            } else {
                this.text = null
                actions = translateToAction(entry, this.loggedIn);
                console.log(actions)
                if (!actions.length || actions.length > 1) {
                    this.botMessage("Sorry, I didn't undertand your request", ERROR)
                    return
                }
                action = actions[0].name
            }

            console.log(action)

            switch (action) {
                case 'register':
                    this.botMessage('Enter your name')
                    this.nextAction = 'register-name'
                    break
                case 'register-name':
                    this.username = entry
                    this.botMessage('Enter the currency code you want to use in your account')
                    this.nextAction = 'register-default-currency'
                    break
                case 'register-default-currency':
                    this.defaultCurrency = entry
                    this.botMessage('Enter your e-mail')
                    this.nextAction = 'register-email'
                    break
                case 'register-email':
                    this.email = entry
                    this.botMessage('Enter your password')
                    this.isTypingPassword = true
                    this.nextAction = 'register-password'
                    break
                case 'register-password':
                    this.password = entry
                    this.botMessage('Enter your password confirmation')
                    this.isTypingPassword = true
                    this.nextAction = 'register-password-confirmation'
                    break
                case 'register-password-confirmation':
                    this.password_confirmation = entry
                    this.botMessage('Trying to register')
                    const registrationBody = {
                        name: this.username,
                        default_currency: this.defaultCurrency,
                        email: this.email,
                        password: this.password,
                        password_confirmation: this.password_confirmation
                    }
                    console.log(registrationBody)
                    this.loading = true
                    axios.post('/api/register', registrationBody).then(response => {
                        console.log(response)
                    }).catch(error => {
                        if (error.response) {
                            console.log(error.response)
                            const {message} = error.response.data
                            this.botMessage(message, ERROR)
                        }
                    }).finally(() => this.loading = false)
                    this.nextAction = null
                    this.clearValues()
                    break
                case 'login':
                    this.botMessage('Enter your email')
                    this.nextAction = 'enter-password'
                    break
                case 'enter-password':
                    this.username = entry
                    this.botMessage('Please enter your password')
                    this.isTypingPassword = true
                    this.nextAction = 'try-login'
                    break
                case 'try-login':
                    this.isTypingPassword = false
                    this.nextAction = null
                    this.loading = true
                    axios.post('/api/login', {
                        email: this.username,
                        password: entry
                    }).then(response => {
                        console.log(response);
                        axios.get('/api/test-auth').then(_ => console.log(_))
                        this.botMessage('Login success!', SUCCESS)
                        this.loggedIn = true
                        this.clearValues()
                    }).catch(error => {
                        if (error.response) {
                            const {type, message} = error.response.data
                            this.botMessage(message, type)
                        } else {
                            this.botMessage('Somethign went wrong!', ERROR)
                        }
                    }).finally(() => this.loading = false)
                    break
                case 'deposit':
                    let extractedAmmount = util.extractMoney(entry)
                    if (extractedAmmount) {
                        this.ammount = extractedAmmount
                        this.currencyCode = util.extractCurrencyCode(entry)
                        this.botMessage('Trying to send your deposit of ' + this.ammount + " " + this.currencyCode)
                        this.clearValues()
                    } else {
                        this.botMessage('Enter the ammout you want to deposit')
                        this.nextAction = 'deposit-ask-ammount'
                    }
                    break
                case 'deposit-ask-ammount':
                    let ammountAsked = util.extractMoney(entry)
                    this.ammount = ammountAsked
                    this.botMessage('Trying to send your deposit of ' + this.ammount)
                    this.clearValues()
                    break
                case 'withdraw':
                    const withDrawAmmount = util.extractMoney(entry)
                    if (withDrawAmmount) {
                        this.ammount = withDrawAmmount
                        this.botMessage('Trying to send your withdraw of ' + this.ammount)
                        this.clearValues()
                    } else {
                        this.botMessage('Enter the ammout you want to withdraw')
                        this.nextAction = 'deposit-ask-ammount'
                    }
                    break
                    break
                case 'logout':
                    this.loading = true
                    axios.post('/api/logout').then(response => {
                        this.botMessage('Successfully logged out!', SUCCESS)
                        this.loggedIn = false
                    }).finally(() => this.loading = false);
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
            if (this.isTypingPassword) {
                text = PASSWORD_PLACEHOLER
            }
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
            this.defaultCurrency = null
            this.ammount = null
            this.currencyCode = null
        },
    }
};
</script>
