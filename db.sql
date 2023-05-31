-- phpMyAdmin SQL Dump
-- version 4.6.6deb5ubuntu0.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 14, 2022 at 04:28 AM
-- Server version: 10.1.48-MariaDB-0ubuntu0.18.04.1
-- PHP Version: 7.2.24-0ubuntu0.18.04.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `drm`
--

-- --------------------------------------------------------

--
-- Table structure for table `cats`
--

CREATE TABLE `cats` (
  `CatID` int(11) NOT NULL,
  `CatName` varchar(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cats`
--

INSERT INTO `cats` (`CatID`, `CatName`) VALUES
(1, 'General');

-- --------------------------------------------------------

--
-- Table structure for table `channels`
--

CREATE TABLE `channels` (
  `ID` int(11) NOT NULL,
  `CatID` int(11) NOT NULL DEFAULT '1',
  `ChannelName` varchar(64) NOT NULL,
  `Manifest` varchar(1024) NOT NULL,
  `VariantID` int(11) DEFAULT NULL,
  `DownloadPath` varchar(128) DEFAULT NULL,
  `StartTime` datetime DEFAULT NULL,
  `EndTime` datetime DEFAULT NULL,
  `Status` varchar(16) NOT NULL DEFAULT 'Stopped',
  `PID` int(11) NOT NULL DEFAULT '0',
  `FPID` int(11) DEFAULT '0',
  `info` text,
  `SegmentJoiner` int(11) DEFAULT '0',
  `PlaylistLimit` int(11) DEFAULT '0',
  `URLListLimit` int(11) DEFAULT '0',
  `UseProxy` tinyint(4) DEFAULT '0',
  `ProxyURL` varchar(128) DEFAULT NULL,
  `ProxyPort` int(11) DEFAULT NULL,
  `ProxyUser` varchar(64) DEFAULT NULL,
  `ProxyPass` varchar(64) DEFAULT NULL,
  `DownloadUseragent` varchar(255) DEFAULT NULL,
  `AudioID` varchar(255) DEFAULT NULL,
  `VideoID` varchar(64) DEFAULT NULL,
  `AllowedIP` text,
  `AutoRestart` tinyint(4) DEFAULT '1',
  `Output` varchar(8) NOT NULL DEFAULT 'hls',
  `UDPIP` varchar(32) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `channels`
--

INSERT INTO `channels` (`ID`, `CatID`, `ChannelName`, `Manifest`, `VariantID`, `DownloadPath`, `StartTime`, `EndTime`, `Status`, `PID`, `FPID`, `info`, `SegmentJoiner`, `PlaylistLimit`, `URLListLimit`, `UseProxy`, `ProxyURL`, `ProxyPort`, `ProxyUser`, `ProxyPass`, `DownloadUseragent`, `AudioID`, `VideoID`, `AllowedIP`, `AutoRestart`, `Output`, `UDPIP`) VALUES
(1, 1, 'bein prem1', 'https://live-d-main-beinmena.beincdn.com/ch10/b96db17d-e0cd-4a40-8c9f-5911d83f6341/ea42ec8d-adfc-4ef4-8981-799f11d07cc8.ism/ZXhwPTE2NDk5MDA4Njh%2bYWNsPSUyZmNoMTAlMmZiOTZkYjE3ZC1lMGNkLTRhNDAtOGM5Zi01OTExZDgzZjYzNDElMmZlYTQyZWM4ZC1hZGZjLTRlZjQtODk4MS03OTlmMTFkMDdjYzguaXNtJTJmKn5kYXRhPWY0OTcxYmE1LWJiMDUtNDViMS1iNTVkLWM3ODRiNGM5MzE1NX5obWFjPWY2MDM4YmM0NDNmNGI0OWExMWViOGNiNTk4Y2VjOWVhZDQ4Njc2MGIwMGVmN2U3ODAwYzgzMDg1OGVjMjU3ZWY%3d/manifest(format=mpd-time-csf,filter=desktop-fullres,encryption=cenc)', NULL, NULL, NULL, '2022-04-14 04:23:25', 'Stopped', 0, 0, '', 0, 0, 0, 0, NULL, NULL, NULL, NULL, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.75 Safari/537.36', '5_A_primary_12037987828332995369', '1_V_video_17090713503055598136', '[\"\"]', 1, 'hls', ''),
(2, 1, 'jawwy', 'https://akm.cdn.intigral-ott.net/JAW1/JAW1.isml/manifest.mpd', NULL, NULL, NULL, '2022-04-14 04:23:25', 'Stopped', 0, 0, '', 0, 0, 0, 0, NULL, NULL, NULL, NULL, '', 'audio_96052_ara=96000', 'video=3000000', '[\"*\"]', 1, 'hls', ''),
(3, 1, 'FOX REW', 'https://akm.cdn.intigral-ott.net/FXR/FXR.isml/manifest.mpd', NULL, NULL, NULL, '2022-04-14 04:23:25', 'Stopped', 0, 0, '', 0, 0, 0, 0, NULL, NULL, NULL, NULL, '', 'audio_96051_eng=96000', 'video=3000000', '[\"*\"]', 1, 'hls', '');

-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- Table structure for table `channel_keys`
--

CREATE TABLE `channel_keys` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ChannelID` int(11) NOT NULL,
  `KID` varchar(32) NOT NULL,
  `Key` varchar(32) NOT NULL,
  PRIMARY KEY (ID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

INSERT INTO `channel_keys` (`ID`, `ChannelID`, `KID`, `Key`) VALUES
(1, 1, '9C4DF6B783964B6995628CF95C2A6EB3', '61a4b148c4b0ee4ea27c50cbf8793a78'),
(2, 2, 'E74C7D697BD26595B196C6B96E2D4F5B', 'e288110233737502e8af9184098ab50b'),
(3, 3, '556AF5561CBE3405FC3C241FD23B36C2', '18e9a528c7a22b940122afc832c333bf');

-- --------------------------------------------------------

--
-- Table structure for table `channel_keys`
--

CREATE TABLE `channel_headers` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ChannelID` int(11) NOT NULL,
  `Value` varchar(255) NOT NULL,
  PRIMARY KEY (ID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;


--
-- Table structure for table `config`
--

CREATE TABLE `config` (
  `ID` int(11) NOT NULL,
  `ConfigName` varchar(32) NOT NULL,
  `ConfigDesc` varchar(64) NOT NULL,
  `ConfigValue` varchar(256) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `config`
--

INSERT INTO `config` (`ID`, `ConfigName`, `ConfigDesc`, `ConfigValue`) VALUES
(1, 'DownloadPath', '', '/var/www/html/download'),
(2, 'BinPath', '', '/var/www/html/'),
(3, 'SegmentJoiner', '', '8'),
(4, 'DeleteEncryptedAfterDecrypt', '', '1'),
(5, 'DeleteDecryptedAfterMerge', '', '1'),
(6, 'PlaylistLimit', '', '8'),
(7, 'URLListLimit', '', '4'),
(8, 'FFMpegCMD', '', '-hide_banner -i [VIDEO] -i [AUDIO] -vcodec copy -scodec copy -acodec copy [OUTPUT]'),
(9, 'M3UDownloadURL', '', 'http://127.0.0.1/download'),
(10, 'ProxyURL', 'Proxy URL', ''),
(11, 'ProxyPort', 'Proxy Port', ''),
(12, 'ProxyUser', 'Proxy Username', ''),
(13, 'ProxyPass', 'Proxy Password', ''),
(14, 'DownloadUseragent', 'Download Useragent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0'),
(15, 'AutoRestart', 'Auto restart channels', '1'),
(16, 'DownloaderPath', 'Downloader Path', '/var/www/html/'),
(17, 'BackupPath', '/var/www/backup', '/var/www/backup'),
(18, 'MaxLogSize', '', '1048576');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `ID` int(11) NOT NULL,
  `Titel` varchar(32) DEFAULT NULL,
  `Msg` varchar(256) DEFAULT NULL,
  `Sent` datetime NOT NULL,
  `Status` varchar(8) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `UserID` varchar(16) NOT NULL,
  `Password` varchar(32) NOT NULL,
  `Role` varchar(16) DEFAULT NULL,
  `LastAccess` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `UserID`, `Password`, `Role`, `LastAccess`) VALUES
(1, 'admin', 'Admin@2023##', 'Admin', '2022-04-14 03:44:24');

-- --------------------------------------------------------

--
-- Table structure for table `variant`
--

CREATE TABLE `variant` (
  `ID` int(11) NOT NULL,
  `ChannelID` int(11) NOT NULL,
  `Language` varchar(8) DEFAULT NULL,
  `Bandwidth` int(11) DEFAULT NULL,
  `AudioID` varchar(32) NOT NULL,
  `AudioBandwidth` int(11) DEFAULT NULL,
  `AudioCodecs` varchar(32) DEFAULT NULL,
  `VideoID` varchar(32) NOT NULL,
  `VideoBandwidth` int(11) DEFAULT NULL,
  `VideoCodecs` varchar(32) DEFAULT NULL,
  `Width` int(11) DEFAULT NULL,
  `Height` int(11) DEFAULT NULL,
  `Framerate` varchar(16) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `variant`
--

INSERT INTO `variant` (`ID`, `ChannelID`, `Language`, `Bandwidth`, `AudioID`, `AudioBandwidth`, `AudioCodecs`, `VideoID`, `VideoBandwidth`, `VideoCodecs`, `Width`, `Height`, `Framerate`) VALUES
(38, 1, 'mul', 0, '5_A_secondary_200482043670721364', 96000, 'mp4a.40.2', '1_V_video_6140004908920393176', 400000, 'avc1.4D400D', 384, 216, ''),
(37, 1, 'mul', 0, '5_A_secondary_200482043670721364', 96000, 'mp4a.40.2', '1_V_video_10384369079951789487', 600000, 'avc1.4D4015', 512, 288, ''),
(36, 1, 'mul', 0, '5_A_secondary_200482043670721364', 96000, 'mp4a.40.2', '1_V_video_15797029007959740831', 949952, 'avc1.4D401E', 640, 360, ''),
(35, 1, 'mul', 0, '5_A_secondary_200482043670721364', 96000, 'mp4a.40.2', '1_V_video_14851006325304870635', 1400000, 'avc1.4D401E', 768, 432, ''),
(34, 1, 'mul', 0, '5_A_secondary_200482043670721364', 96000, 'mp4a.40.2', '1_V_video_16981495139092747609', 2200000, 'avc1.4D401F', 1024, 576, ''),
(33, 1, 'mul', 0, '5_A_secondary_200482043670721364', 96000, 'mp4a.40.2', '1_V_video_17090713503055598136', 3449984, 'avc1.64001F', 1280, 720, ''),
(32, 1, 'mul', 0, '5_A_secondary_200482043670721364', 96000, 'mp4a.40.2', '1_V_video_15517948687693901110', 4749952, 'avc1.640028', 1600, 900, ''),
(31, 1, 'mul', 0, '5_A_secondary_200482043670721364', 96000, 'mp4a.40.2', '1_V_video_14759481473095519504', 6000000, '', 1920, 1080, ''),
(30, 1, 'mul', 0, '5_A_primary_12037987828332995369', 96000, 'mp4a.40.2', '1_V_video_3750956353252827751', 149952, 'avc1.4D400C', 256, 144, ''),
(29, 1, 'mul', 0, '5_A_primary_12037987828332995369', 96000, 'mp4a.40.2', '1_V_video_17917517102610242332', 249984, 'avc1.4D400D', 384, 216, ''),
(28, 1, 'mul', 0, '5_A_primary_12037987828332995369', 96000, 'mp4a.40.2', '1_V_video_6140004908920393176', 400000, 'avc1.4D400D', 384, 216, ''),
(27, 1, 'mul', 0, '5_A_primary_12037987828332995369', 96000, 'mp4a.40.2', '1_V_video_10384369079951789487', 600000, 'avc1.4D4015', 512, 288, ''),
(26, 1, 'mul', 0, '5_A_primary_12037987828332995369', 96000, 'mp4a.40.2', '1_V_video_15797029007959740831', 949952, 'avc1.4D401E', 640, 360, ''),
(25, 1, 'mul', 0, '5_A_primary_12037987828332995369', 96000, 'mp4a.40.2', '1_V_video_14851006325304870635', 1400000, 'avc1.4D401E', 768, 432, ''),
(24, 1, 'mul', 0, '5_A_primary_12037987828332995369', 96000, 'mp4a.40.2', '1_V_video_16981495139092747609', 2200000, 'avc1.4D401F', 1024, 576, ''),
(23, 1, 'mul', 0, '5_A_primary_12037987828332995369', 96000, 'mp4a.40.2', '1_V_video_17090713503055598136', 3449984, 'avc1.64001F', 1280, 720, ''),
(22, 1, 'mul', 0, '5_A_primary_12037987828332995369', 96000, 'mp4a.40.2', '1_V_video_15517948687693901110', 4749952, 'avc1.640028', 1600, 900, ''),
(21, 1, 'mul', 0, '5_A_primary_12037987828332995369', 96000, 'mp4a.40.2', '1_V_video_14759481473095519504', 6000000, '', 1920, 1080, ''),
(39, 1, 'mul', 0, '5_A_secondary_200482043670721364', 96000, 'mp4a.40.2', '1_V_video_17917517102610242332', 249984, 'avc1.4D400D', 384, 216, ''),
(40, 1, 'mul', 0, '5_A_secondary_200482043670721364', 96000, 'mp4a.40.2', '1_V_video_3750956353252827751', 149952, 'avc1.4D400C', 256, 144, ''),
(41, 2, 'ar', 0, 'audio_96052_ara=96000', 96000, 'mp4a.40.2', 'video=350000', 350000, 'avc1.4D401F', 384, 216, '25'),
(42, 2, 'ar', 0, 'audio_96052_ara=96000', 96000, 'mp4a.40.2', 'video=600000', 600000, 'avc1.4D401F', 512, 288, '25'),
(43, 2, 'ar', 0, 'audio_96052_ara=96000', 96000, 'mp4a.40.2', 'video=1300000', 1300000, 'avc1.4D401F', 768, 432, '25'),
(44, 2, 'ar', 0, 'audio_96052_ara=96000', 96000, 'mp4a.40.2', 'video=1800000', 1800000, 'avc1.4D401F', 960, 540, '25'),
(45, 2, 'ar', 0, 'audio_96052_ara=96000', 96000, 'mp4a.40.2', 'video=3000000', 3000000, 'avc1.640029', 1280, 720, '25'),
(46, 3, 'en', 0, 'audio_96051_eng=96000', 96000, 'mp4a.40.2', 'video=350000', 350000, 'avc1.4D401F', 384, 216, '25'),
(47, 3, 'en', 0, 'audio_96051_eng=96000', 96000, 'mp4a.40.2', 'video=600000', 600000, 'avc1.4D401F', 512, 288, '25'),
(48, 3, 'en', 0, 'audio_96051_eng=96000', 96000, 'mp4a.40.2', 'video=1300000', 1300000, 'avc1.4D401F', 768, 432, '25'),
(49, 3, 'en', 0, 'audio_96051_eng=96000', 96000, 'mp4a.40.2', 'video=1800000', 1800000, 'avc1.4D401F', 960, 540, '25'),
(50, 3, 'en', 0, 'audio_96051_eng=96000', 96000, 'mp4a.40.2', 'video=3000000', 3000000, 'avc1.640029', 1280, 720, '25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cats`
--
ALTER TABLE `cats`
  ADD PRIMARY KEY (`CatID`);

--
-- Indexes for table `channels`
--
ALTER TABLE `channels`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `CatID` (`CatID`);


ALTER TABLE `channel_keys`
  ADD KEY `ChannelId` (`ChannelId`);


ALTER TABLE `channel_headers`
  ADD KEY `ChannelId` (`ChannelId`);

--
-- Indexes for table `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `variant`
--
ALTER TABLE `variant`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cats`
--
ALTER TABLE `cats`
  MODIFY `CatID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `channels`
--
ALTER TABLE `channels`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `config`
--
ALTER TABLE `config`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `variant`
--
ALTER TABLE `variant`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;
