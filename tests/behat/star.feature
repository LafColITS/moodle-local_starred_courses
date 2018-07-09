@local @local_starred_courses
Feature:

  @javascript
  Scenario: Starring and unstarring courses
    And the following "courses" exist:
      | fullname | shortname |
      | Test One | test1     |
      | Test Two | test2     |

    When I log in as "admin"
    And I am on site homepage
    And I follow "Test One"
    Then the "class" attribute of "a[data-key=starlink]" "css_element" should contain "course-unstarred"
    When I click on "a[data-key=starlink]" "css_element"
    Then the "class" attribute of "a[data-key=starlink]" "css_element" should contain "course-starred"
    When I reload the page
    Then the "class" attribute of "a[data-key=starlink]" "css_element" should contain "course-starred"

    And I am on site homepage
    When I follow "Test Two"
    Then the "class" attribute of "a[data-key=starlink]" "css_element" should contain "course-unstarred"
    When I click on "a[data-key=starlink]" "css_element"
    Then the "class" attribute of "a[data-key=starlink]" "css_element" should contain "course-starred"
    When I reload the page
    Then the "class" attribute of "a[data-key=starlink]" "css_element" should contain "course-starred"
    When I click on "a[data-key=starlink]" "css_element"
    Then the "class" attribute of "a[data-key=starlink]" "css_element" should contain "course-unstarred"
    When I reload the page
    Then the "class" attribute of "a[data-key=starlink]" "css_element" should contain "course-unstarred"
