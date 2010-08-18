#!/bin/sh
#
# Simple script that dumps the database scheme to SQL and XML-files.
#
# Author: Anders L�vgren
# Date:   2010-05-17
#

mysqldump -u root -p -d openexam > openexam.sql
mysqldump -u root -p -d -X openexam > openexam.xml
