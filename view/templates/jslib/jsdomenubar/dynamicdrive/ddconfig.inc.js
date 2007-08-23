function createjsDOMenu() {
  mainMenu1 = new jsDOMenu(130);
  with (mainMenu1) {
    addMenuItem(new menuItem("Dynamic Drive", "", "http://www.dynamicdrive.com"));
    addMenuItem(new menuItem("JavaScript Kit", "", "http://www.javascriptkit.com"));
    addMenuItem(new menuItem("Coding Forums", "", "http://www.codingforums.com"));
  }
  
  mainMenu2 = new jsDOMenu(110);
  with (mainMenu2) {
    addMenuItem(new menuItem("Google", "", "http://www.google.com"));
    addMenuItem(new menuItem("Yahoo", "", "http://www.yahoo.com"));
    addMenuItem(new menuItem("Teoma", "", "http://www.teoma.com"));
  }

  mainMenu3 = new jsDOMenu(140);
  with (mainMenu3) {
    addMenuItem(new menuItem("CNN", "", "http://www.cnn.com"));
    addMenuItem(new menuItem("MSNBC", "", "http://www.msnbc.com"));
    addMenuItem(new menuItem("BBC News", "", "http://news.bbc.co.uk"));
    addMenuItem(new menuItem("-"));
    addMenuItem(new menuItem("Tech News", "item4", ""));
  }

  mainMenu3_1 = new jsDOMenu(150);
  with (mainMenu3_1) {
    addMenuItem(new menuItem("News.com", "", "http://www.news.com"));
    addMenuItem(new menuItem("Wired", "", "http://www.wired.com"));
    addMenuItem(new menuItem("PC World", "", "http://www.pcworld.com"));
  }

	mainMenu3.items.item4.setSubMenu(mainMenu3_1); //ASSOCIATE SUB MENU WITH PARENT MENU ITEM
  
  menuBar = new jsDOMenuBar(); //CREATE MAIN MENU ITEMS
  with (menuBar) {
    addMenuBarItem(new menuBarItem("Webmaster", mainMenu1));
    addMenuBarItem(new menuBarItem("Search Engines", mainMenu2));
    addMenuBarItem(new menuBarItem("News", mainMenu3));
  }
  menuBar.moveTo(10, 10);
}