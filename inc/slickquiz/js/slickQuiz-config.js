// Setup your quiz text and questions here


var quizJSON = {
	"info": {
		"name":    "CH.1 Test",
		"main":    "<p>Hope you studied!?</p>",
		"results": "<h5>GRATS</h5><p>YOU KNOW ABOUT VAPES</p>",
		"level1":  "Vapornation King",
		"level2":  "Vapornation City Council",
		"level3":  "Vapornation Citizen",
		"level4":  "Vapornation Town Idiot",
		"level5":  "You didn't even read you idiot."
		},
	"questions": [
	{
		"q": "Fill in the blank: The process of heating plant material gradually ______ the point at which it burns is called?",
		"a": [
			{"option": "Below|",     "correct": true} 
		],
		"text_question": true,
		"correct": "<p><span>Wow good job!</span> </p>",
		"incorrect": "<p><span>The correct answer is 'below'.</span></p>" 
	},
	{
		"q": "When combustion occurs, what is created ? _______",
		"a": [
			{"option": "Smoke|",  "correct": true}
		],
		"text_question": true,
		"correct": "<p><span>Nice!</span></p>",
		"incorrect": "<p><span>Uhh no.</span>The correct answer is 'smoke'</p>" 
	},
	{ 
		"q": "What are the three basic types of vaporizers",
		"a": [
			{"option": "Desktop",                "correct": true},
			{"option": "Bionic",           			"correct": false},
			{"option": "Portable",           			"correct": true},
			{"option": "Pens",           		"correct": true} 
		],
		"force_checkbox":true,
		"correct": "<p><span>Nice!</span> </p>",
		"incorrect": "<p><span>Hmmm.</span> The correct answer is desktop, portables, and pens.</p>" 
	},
	{ 
		"q": "Check all that apply: Vaporizers help the user achieve the same desired effects of smoking without the harmful________ ",
		"a": [
			{"option": "Smoke",                "correct": true},
			{"option": "Carcinogens",           			"correct": true},
			{"option": "Tricroms",           			"correct": false},
			{"option": "By-products",           		"correct": true} 
		],
		"force_checkbox": true,
		"correct": "<p><span>Brilliant!</span> You're  a genius.</p>",
		"incorrect": "<p><span>Not Quite, the answer is A,B, and D.</span></p>" 
	},	
	{ 
		"q": "The process of burning something is called?",
		"a": [
			{"option": "Convection",         "correct": false},
			{"option": "Conduction",     "correct": false},
			{"option": "Reduction",        "correct": false},
			{"option": "Combustion",        "correct": true} 
		],
		"correct": "<p><span>Good Job!</span></p>",
		"incorrect": "<p><span>Sorry.</span> The correct answer is Combustion.</p>" 
	},
	{ 
		"q": "The heating method in which plant material comes into direct contact with a heat source is called?",
		"a": [
			{"option": "Convection",         "correct": false},
			{"option": "Conduction",     "correct": true},
			{"option": "Reduction",        "correct": false},
			{"option": "Combustion",        "correct": false} 
		],
		"correct": "<p><span>Holy bananas!</span> I didn't actually expect you to know that! Correct!</p>",
		"incorrect": "<p><span>Sorry.</span> The correct answer is Conduction.</p>" 
	},
	{ 
		"q": "The heating method in which heated air passes through and around the plant material is called??",
		"a": [
			{"option": "Convection",         "correct": true},
			{"option": "Conduction",     "correct": false},
			{"option": "Reduction",        "correct": false},
			{"option": "Combustion",        "correct": false} 
		],
		"correct": "<p><span>Good Job!</span> </p>",
		"incorrect": "<p><span>ERRRR!</span> The correct answer is convection!</p>"
	},	
	{ 
		"q": "Which heating method is most conducive to true vaporization??",
		"a": [
			{"option": "Convection",         "correct": true},
			{"option": "Conduction",     "correct": false}

		],
		"correct": "<p><span>Good Job!</span> </p>",
		"incorrect": "<p><span>ERRRR!</span> The correct answer is convection!</p>"
	},
	{ 
		"q": "Which of the following is NOT a reason why people vaporize??",
		"a": [
			{"option": "Discreetness",     "correct": false},
			{"option": "It gets you higher",         "correct": true},
			{"option": "Flavor",        "correct": false},
			{"option": "Health Benefits",        "correct": false} 
		],
		"correct": "<p><span>Good Job!</span> </p>",
		"incorrect": "<p><span>LOL!</span> Good one!</p>"
	},
		{ 
		"q": "What is the ideal temperature range at which to vaporize??",
		"a": [
			{"option": "225-250 Degrees",         "correct": false},
			{"option": "350-395 Degrees",     "correct": true},
			{"option": "150-200 Degrees",        "correct": false},
			{"option": "450-475 Degrees",        "correct": false} 
		],
		"correct": "<p><span>Good Job!</span> </p>",
		"incorrect": "<p><span>ERRRR!</span> The correct answer is 350-395!</p>"
	}   
	]
};
