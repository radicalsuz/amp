function flag_upto(id)
{
	elements = document.forms[0].elements;
	
	for(i=0; i<elements.length; i++)
	{
		elements[i].checked = true;
		
		if(elements[i].name == id)
		{
			break;
		}
	}
}

function flag_all()
{
	elements = document.forms[0].elements;
	
	for(i=0; i<elements.length; i++)
	{
		elements[i].checked = true;
	}
}

function unflag_all()
{
	elements = document.forms[0].elements;
	
	for(i=0; i<elements.length; i++)
	{
		elements[i].checked = false;
	}
}

function mark_read()
{
	document.items['action'].value = 'read';
	document.items['return'].value = escape(location);
	document.items.submit();
}

function mark_unread()
{
	document.items['action'].value = 'unread';
	document.items['return'].value = escape(location);
	document.items.submit();
}
