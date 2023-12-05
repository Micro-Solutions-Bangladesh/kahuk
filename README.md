# kahuk-com
Kahuk offers to bookmark endless articles, conversations, images, and videos using web URLs. The community can comment on posts that provide discussion and often humor.

***

## What Happens in The CMS

> #### User Register
> 
> When a new user register he/she will be sent an email to verify the email address is correct before the new account count as a registered account.


> #### Fork Story
> 
> When a user fork a story, the story will be visible in his/her profile page as a forked story. In the process, the CMS also update few Karma numbers:
> 
> - Increase story karma by the numbers mentioned as `FORK_KARMA_FOR_STORY`
> - Increase karma for the session user by the numbers mentioned as `FORK_KARMA_FOR_USER`


> #### Story Reaction
> 
> - When a user react to a story, the story will be visible in his/her profile page with the Reaction information. In the process, the CMS also update few Karma numbers:
> 
> - Increase story karma by the numbers mentioned as `REACTION_KARMA_FOR_STORY`
> - Increase karma for the session user by the numbers mentioned as `REACTION_KARMA_FOR_USER`


> #### Story Submit
> 
> - When a new story submited, the story initially shows in as a new story and also in the trending story pages. By the time when a story karma reach equal or higher then `NEW_TO_PUBLISHED_KARMA` the story will be appear in publish story and trending story pages depending on the submit time and karma value.
> 
> In the process of new story submit, the CMS also update few Karma numbers:
> 
> - Initially the story karma will be `NEW_STORY_KARMA_INITIALY`.
> - Increase karma for the session user by the numbers mentioned as `NEW_STORY_KARMA_FOR_USER`

