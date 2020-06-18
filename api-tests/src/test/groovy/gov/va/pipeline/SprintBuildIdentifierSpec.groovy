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
            param                                   || expectedSprintBuildIdentifier
            [date: LocalDate.of(2020, 1, 1)]        || "BLD2020-01-07XX"
            [date: LocalDate.of(2020, 1, 6)]        || "BLD2020-01-07XX"
            [date: LocalDate.of(2020, 1, 7)]        || "BLD2020-01-07XX"
//            [date: LocalDate.of(2020, 1, 8)]        || "BLD2020-01-21XX"
//            [date: LocalDate.of(2020, 1, 22)]       || "BLD2020-02-04XX"
    }




}