const actions = [
    {name: "login", triggers: "login log-in", mustBeLoggedin: false},
    {name: "register", triggers: "signup register", mustBeLoggedin: false},
    {name: "deposit", triggers: "deposit", mustBeLoggedin: true},
    {name: "withdraw", triggers: "withdraw", mustBeLoggedin: true},
    {name: "account-balance", triggers: "balance funds", mustBeLoggedin: true},
    {name: "logout", triggers: "logout", mustBeLoggedin: true},
    {name: "help", triggers: "help", mustBeLoggedin: false},
]

const translateToAction = (text) => {
    let sanitizedText = text.toLocaleLowerCase().replace("-", "")

    let action = actions.filter(action => {
        let match = false
        action.triggers.split(" ").forEach(trigger => {
            if (sanitizedText.indexOf(trigger) !== -1) {
                match = true
            }
        })
        return match
    });

    return action
}

exports.translateToAction = translateToAction
