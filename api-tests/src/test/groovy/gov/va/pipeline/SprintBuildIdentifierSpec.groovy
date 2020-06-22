package gov.va.pipeline

import spock.lang.Specification
import spock.lang.Unroll

import java.time.*

import static org.hamcrest.Matchers.*
import static org.hamcrest.MatcherAssert.assertThat
import static org.hamcrest.Matchers.equalTo


class SprintBuildIdentifierSpec extends Specification {


    static def SprintBuildIdentifer(ofDate = LocalDate.now())
    {


        def buildPrefix = "BLD"
        def buildSuffix = "XX"
        def lastDayOfSprint = LocalDate.now().withDayOfYear(7)


        /*
          Write algorithm here
         */


        return "${buildPrefix}${lastDayOfSprint}${buildSuffix}".toString()
    }


    @Unroll
    def "unit tests"() {
        given:
            def paramDate = param.date
        when:
            def pipelineBuildIdentifier = SprintBuildIdentifer(paramDate)
        then:
            assertThat(pipelineBuildIdentifier, equalTo(expectedSprintBuildIdentifier))
        where:
            param                                     || expectedSprintBuildIdentifier
            [date: LocalDate.of(2020, 6, 22)]         || "BLD2020-06-30XX"
            [date: LocalDate.of(2020, 6, 30)]         || "BLD2020-06-30XX"
            [date: LocalDate.of(2020, 7, 1)]          || "BLD2020-07-14XX"
            [date: LocalDate.of(2020, 7, 3)]          || "BLD2020-07-14XX"
            [date: LocalDate.of(2020, 7, 13)]         || "BLD2020-07-14XX"
            [date: LocalDate.of(2020, 7, 14)]         || "BLD2020-07-14XX"
            [date: LocalDate.of(2020, 7, 15)]         || "BLD2020-07-28XX"
    }




}
