
			
			function validate()
			{
				if (document.forms.signup.name.value == "")
				{
					alert("You must provide a user name!");
					return false;
				}
				else if (document.forms.signup.email.value == "")
				{
					alert("You must provide a user email!");
					return false;
				}
				else if (document.forms.signup.password1.value == "")
				{
					alert("You must provide a password!");
					return false;					
				}
				else if (document.forms.signup.password1.value != document.forms.signup.password2.value)
				{
					alert("You must provide the same password twice!");
					return false;
				}
				else if (!document.forms.signup.agreement.checked)
				{
					alert("You must agree to our terms and conditions!");
					return false;
				}
				return true;
			}
		