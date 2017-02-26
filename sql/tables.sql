USE blog;

CREATE TABLE IF NOT EXISTS `comments` (
      `commentId` int(11) NOT NULL AUTO_INCREMENT,
      `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `commentorName` varchar(150) NOT NULL,
      `commentorMail` varchar(250) NOT NULL,
      `commentorPage` varchar(250) DEFAULT NULL,
      `comment` text NOT NULL,
      PRIMARY KEY (`commentId`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `languages` (
      `languageId` int(11) NOT NULL AUTO_INCREMENT,
      `language` varchar(20) NOT NULL,
      `icon` varchar(250) NOT NULL,
      PRIMARY KEY (`languageId`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `users` (
      `userId` int(11) NOT NULL AUTO_INCREMENT,
      `userAlias` varchar(250) NOT NULL,
      `userMail` varchar(250) NOT NULL,
      `userPass` varchar(250) NOT NULL,
      `status` varchar(10) NOT NULL,
      `salt` varchar(128) NOT NULL,
      PRIMARY KEY (`userId`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `articles` (
      `articleId` int(11) NOT NULL AUTO_INCREMENT,
      `status` varchar(15) NOT NULL,
      `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `userId` int(11) NOT NULL,
      PRIMARY KEY (`articleId`),
      KEY `userId` (`userId`),
      CONSTRAINT `articles_userid` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `content` (
      `contentId` int(11) NOT NULL AUTO_INCREMENT,
      `articleId` int(11) NOT NULL,
      `languageId` int(11) NOT NULL,
      `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `heading` varchar(500) NOT NULL,
      `content` text NOT NULL,
      PRIMARY KEY (`contentId`),
      KEY `articleId` (`articleId`),
      KEY `languageId` (`languageId`),
      CONSTRAINT `content_articleid` FOREIGN KEY (`articleId`) REFERENCES `articles` (`articleId`),
      CONSTRAINT `content_languageid` FOREIGN KEY (`languageId`) REFERENCES `languages` (`languageId`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `categories` (
      `categoryId` int(11) NOT NULL AUTO_INCREMENT,
      `categoryName` varchar(250) NOT NULL,
      `parentId` int(11) DEFAULT NULL,
      PRIMARY KEY (`categoryId`),
      KEY `parentId` (`parentId`),
      CONSTRAINT `categories_parentid` FOREIGN KEY (`parentId`) REFERENCES `categories` (`categoryId`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `articlecomments` (
      `articleId` int(11) NOT NULL,
      `commentId` int(11) NOT NULL,
      KEY `articleId` (`articleId`),
      KEY `commentId` (`commentId`),
      CONSTRAINT `articlecomments_articleid` FOREIGN KEY (`articleId`) REFERENCES `articles` (`articleId`),
      CONSTRAINT `articlecomments_commentid` FOREIGN KEY (`commentId`) REFERENCES `comments` (`commentId`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `articlecategories` (
      `articleId` int(11) NOT NULL,
      `categoryId` int(11) NOT NULL,
      KEY `articleId` (`articleId`),
      KEY `categoryId` (`categoryId`),
      CONSTRAINT `articlecategories_articleid` FOREIGN KEY (`articleId`) REFERENCES `articles` (`articleId`),
      CONSTRAINT `articlecategories_categoryid` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`categoryId`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

