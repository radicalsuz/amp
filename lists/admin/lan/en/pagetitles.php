<?
switch ($page) {
  case "home": $page_title = 'Main Admin Page';break;
  case "attributes": $page_title = 'Configure Attributes';break;
	case "stresstest": $page_title = 'Stress Test';break;
  case "list": $page_title = "The List of Lists";break;
  case "editattributes": $page_title = "Configure Attributes";break;
  case "editlist": $page_title = "Edit a list";break;
  case "import4": $page_title = "Import emails from a remote PHPlist database";break;
  case "import3": $page_title = "Import emails from IMAP";break;
  case "import2":
  case "import1":
  case "import": $page_title = "Import emails";break;
  case "export": $page_title = "Export users";break;
  case "initialise": $page_title = "Initialise the database";break;
	case "send": $page_title = "Send a Message";break;
	case "preparesend": $page_title = "Prepare a message for sending";break;
	case "sendprepared": $page_title = "Send a prepared message";break;
	case "members": $page_title = "List Membership";break;
  case "users": $page_title = "List of All users";break;
  case "reconcileusers": $page_title = "Reconcile users";break;
  case "user": $page_title = "Details of a user";break;
  case "messages": $page_title = "All messages";break;
  case "message": $page_title = "View a message";break;
  case "processqueue": $page_title = "Send message queue";break;
  case "defaults": $page_title = "Some useful default attributes";break;
  case "upgrade": $page_title = "Upgrade PHPlist";break;
  case "templates": $page_title = "Templates in the system";break;
  case "template": $page_title = "Add or Edit a template";break;
  case "viewtemplate": $page_title = "Template Preview";break;
  case "configure": $page_title = "Configure PHPlist";break;
  case "admin": $page_title = "Edit an Administrator";break;
  case "admins": $page_title = "List Administrators";break;
  case "adminattributes": $page_title = "Configure Administrator attributes";break;
  case "processbounces": $page_title = "Retrieve bounces from server";break;
  case "bounces": $page_title = "List bounces";break;
  case "bounce": $page_title = 'View a bounce';break;
  case "spageedit": $page_title = "Edit a subscribe page";break;
  case "spage": $page_title = "Subscribe pages";break;
  case "eventlog": $page_title = "Log of events";break;
  case "getrss": $page_title = "Retrieve RSS feeds";break;
  case "viewrss": $page_title = "View RSS Items";break;
  case "community": $page_title = "Welcome to the PHPlist community";break;
  case "vote": $page_title = "Vote for PHPlist";break;
  case "login": $page_title = "Login";break;
  case "logout": $page_title = "Log Out";break;
}
?>
